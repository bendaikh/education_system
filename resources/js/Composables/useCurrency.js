import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useCurrency() {
    const page = usePage();
    
    // Get the current currency from page props, fallback to USD
    const currentCurrency = computed(() => {
        return page.props.settings?.currency || 'USD ($)';
    });
    
    // Currency mapping for symbols and formatting
    const currencyMap = {
        'USD ($)': { symbol: '$', code: 'USD', position: 'before' },
        'EUR (€)': { symbol: '€', code: 'EUR', position: 'before' },
        'GBP (£)': { symbol: '£', code: 'GBP', position: 'before' },
        'CAD (C$)': { symbol: 'C$', code: 'CAD', position: 'before' },
        'AUD (A$)': { symbol: 'A$', code: 'AUD', position: 'before' },
        'MAD (DH)': { symbol: 'DH', code: 'MAD', position: 'after' },
        'JPY (¥)': { symbol: '¥', code: 'JPY', position: 'before' }
    };
    
    // Get currency info for the current currency
    const currencyInfo = computed(() => {
        return currencyMap[currentCurrency.value] || currencyMap['USD ($)'];
    });
    
    /**
     * Format a price with the current currency
     * @param {number|string} amount - The amount to format
     * @param {boolean} showCode - Whether to show currency code
     * @returns {string} - Formatted price string
     */
    const formatPrice = (amount, showCode = false) => {
        // Handle string amounts (remove existing currency symbols)
        let numericAmount = amount;
        if (typeof amount === 'string') {
            // Remove common currency symbols and letters, keep numbers and decimal points
            numericAmount = parseFloat(amount.replace(/[^\d.-]/g, '')) || 0;
        }
        
        const currency = currencyInfo.value;
        const formattedNumber = Number(numericAmount).toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
        
        if (currency.position === 'before') {
            return showCode 
                ? `${currency.symbol}${formattedNumber} ${currency.code}`
                : `${currency.symbol}${formattedNumber}`;
        } else {
            return showCode 
                ? `${formattedNumber} ${currency.symbol} ${currency.code}`
                : `${formattedNumber} ${currency.symbol}`;
        }
    };
    
    /**
     * Convert a price from one currency to another (placeholder for future implementation)
     * @param {number} amount - The amount to convert
     * @param {string} fromCurrency - Source currency
     * @param {string} toCurrency - Target currency
     * @returns {number} - Converted amount
     */
    const convertCurrency = (amount, fromCurrency, toCurrency) => {
        // For now, just return the same amount
        // In the future, this could use exchange rates API
        return amount;
    };
    
    /**
     * Format large numbers (for dashboard statistics)
     * @param {number} amount - The amount to format
     * @returns {string} - Formatted amount (e.g., "1.2M", "150K")
     */
    const formatLargeAmount = (amount) => {
        const currency = currencyInfo.value;
        let formattedAmount;
        
        if (amount >= 1000000) {
            formattedAmount = (amount / 1000000).toFixed(1) + 'M';
        } else if (amount >= 1000) {
            formattedAmount = (amount / 1000).toFixed(1) + 'K';
        } else {
            formattedAmount = amount.toString();
        }
        
        if (currency.position === 'before') {
            return `${currency.symbol}${formattedAmount}`;
        } else {
            return `${formattedAmount} ${currency.symbol}`;
        }
    };
    
    return {
        currentCurrency,
        currencyInfo,
        formatPrice,
        convertCurrency,
        formatLargeAmount
    };
}
