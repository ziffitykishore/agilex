/**
 * These should correspond to the same variables set in
 * _variables.scss
 *
 * Note: the lack of units on these are so we can perform
 * calculations inline (since min-width will increment the
 * breakpoint amount by 1px).
 * This does mean that if you switch the breakpoints to another
 * unit (e.g. REMs), you'll need to update each of the JS files
 * that make use of these.
 */

export default {
  screen__xxs: 320,
  screen__xs: 480,
  screen__s: 640,
  screen__m: 768,
  screen__l: 1024,
  screen__xl: 1440
};
