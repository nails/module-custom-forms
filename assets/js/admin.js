'use strict';

import '../sass/admin.scss';
import FormEdit from './components/FormEdit.js';

(function() {
    window.NAILS.ADMIN.registerPlugin(
        'nails/module-custom-forms',
        'FormEdit',
        new FormEdit()
    );
})();
