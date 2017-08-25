'use babel';

import factorioProvider from './provider';

export default {
    getProvider() {
        // return a single provider, or an array of providers to use together
        return [factorioProvider];
    }
};
