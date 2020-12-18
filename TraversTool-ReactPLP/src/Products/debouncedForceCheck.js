import debounce from 'debounce';
import { forceCheck } from 'react-lazyload';

export const debouncedForceCheck = debounce(forceCheck, 300);
