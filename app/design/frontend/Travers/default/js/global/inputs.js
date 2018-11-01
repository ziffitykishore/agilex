import $ from 'jquery';
import domready from 'domready';

function addInputLabel($input) {
  let inputId = $input.attr('id');

  // No input ID so we will create one and add it to the input.
  if (!inputId) {
    inputId = `input-${Math.random().toString(36).substr(2, 9)}`;
    $input.attr('id', inputId);
  }

  // Inject label for custom input styling.
  $input.after(`<label for="${inputId}"></label>`);
}

// We want the next element to be a label for this control.
const needsInputLabel = $input => !$input.next().is("label");

// Our styling assumes an adjacent label.  Check if there's a containing label.
const hasParentLabel = $input => $input.closest("label").length != 0;

function applyCustomInput($input) {
  if (hasParentLabel($input)) {
    // Let's fall back to browser default input styling
    $input.addClass('custom-input--disabled');
    return;
  }

  // There's no label, or it isn't where we expect it to be. Let's avoid an invisible input.
  if (needsInputLabel($input)) {
    addInputLabel($input);
  }
}

export default function init() {
  const injectCustomInputLabel = event => {
    // if a checkbox or radio element is added, they will trigger the animation event `customInputAdded`
    // we then hook into that event and inject a label (if needed) so that styling is correctly shown
    if (event.animationName == 'customInputAdded') {
      const $input = $(event.target);
      applyCustomInput($input);
    }
  };

  domready(() => {
    // Listen for animation events, to catch DOM added items.
    document.addEventListener('animationstart', injectCustomInputLabel, false);
    document.addEventListener('MSAnimationStart', injectCustomInputLabel, false);
    document.addEventListener('webkitAnimationStart', injectCustomInputLabel, false);

    // Let's also run all existing inputs, in case they already animated.
    $('[type=checkbox], [type=radio]').each(function() {
      applyCustomInput($(this));
    });
  });
};
