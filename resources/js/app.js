
import './bootstrap';

import { HSStaticMethods } from 'preline/non-auto';

// Initialize Preline UI components
function initializePreline() {
    HSStaticMethods.autoInit();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePreline);
} else {
    initializePreline();
}
