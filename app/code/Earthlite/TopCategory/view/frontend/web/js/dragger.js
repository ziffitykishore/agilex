define([], function(){

'use strict';

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError("Cannot call a class as a function");
    }
}

function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor)
            descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
    }
}

function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps)
        _defineProperties(Constructor.prototype, protoProps);
    if (staticProps)
        _defineProperties(Constructor, staticProps);
    return Constructor;
}

// Dependencies
// Drag Class
var drag = document.querySelector('.dragger');
var Drag =
        /*#__PURE__*/
                function () {
                    function Drag(_ref) {
                        var el = _ref.el;

                        _classCallCheck(this, Drag);
                        // Root
                        this.root = el; // Firewall
                        if (!this.root) {
                            return;
                        } // Children elements
                        this.zone = this.root.querySelector('.dragger-zone');
                        this.center = this.zone.querySelector('.center');
                        this.inner = this.root.querySelector('.dragger-inner');
                        this.items = this.root.querySelectorAll('.product'); // Scrollbar

                        this.scrollbar = this.root.querySelector('.dragger-scrollbar');
                        this.scrollbarHandle = this.root.querySelector('.dragger-scrollbar-handle'); // Controls

                        this.prev = this.root.querySelector('.dragger-controls-prev');
                        this.next = this.root.querySelector('.dragger-controls-next'); // Scoping

                        this._onNext = this.onNext.bind(this);
                        this._onPrev = this.onPrev.bind(this);
                        this.origin = [0, 0]; // Index

                        this.index = 0;
                        this.first = this.items[0]; // eslint-disable-line

                        this.length = this.items.length;
                        this.timeout = null; // Progress

                        this.progress = 0; // Maximas & Minimas

                        this.min = 0;
                        this.max = 0; // Pull Value
                        // Authorized Amount Below Min & Above Max

                        this.pullMin = 0;
                        this.pullMax = 0; // Translation Values

                        this.transA = 0;
                        this.transB = 0;
                        this.transC = 0; // Events

                        this.prev.addEventListener('click', this._onPrev);
                        this.next.addEventListener('click', this._onNext); // Document
                        var _iteratorNormalCompletion = true;
                        var _didIteratorError = false;
                        var _iteratorError = undefined;

                        try {
                            for (var _iterator = this.items[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                                var item = _step.value;
                                // Get Links & Images of Products
                                var els = item.querySelectorAll('img, a'); // Disable Drag

                                var _iteratorNormalCompletion2 = true;
                                var _didIteratorError2 = false;
                                var _iteratorError2 = undefined;

                                try {
                                    for (var _iterator2 = els[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                                        var child = _step2.value;
                                        // Set Attribute to `false`
                                        child.setAttribute('draggable', false); // Disable Event

                                        child.addEventListener('dragstart', function (e) {
                                            return e.preventDefault();
                                        });
                                    }
                                } catch (err) {
                                    _didIteratorError2 = true;
                                    _iteratorError2 = err;
                                } finally {
                                    try {
                                        if (!_iteratorNormalCompletion2 && _iterator2.return != null) {
                                            _iterator2.return();
                                        }
                                    } finally {
                                        if (_didIteratorError2) {
                                            throw _iteratorError2;
                                        }
                                    }
                                }
                            } // Initial Resize/Render

                        } catch (err) {
                            _didIteratorError = true;
                            _iteratorError = err;
                        } finally {
                            try {
                                if (!_iteratorNormalCompletion && _iterator.return != null) {
                                    _iterator.return();
                                }
                            } finally {
                                if (_didIteratorError) {
                                    throw _iteratorError;
                                }
                            }
                        }

                        this.onResize();
                    }

                    _createClass(Drag, [{
                            key: "onResize",
                            value: function onResize() {
                                // Zone Bound
                                this.zoneBound = this.center.getBoundingClientRect(); // Zone Padding

                                var centerStyle = getComputedStyle(this.center, null);
                                var centerPaddingLeft = parseFloat(centerStyle.getPropertyValue('padding-left'));
                                var centerPaddingRight = parseFloat(centerStyle.getPropertyValue('padding-right'));
                                var itemStyle = getComputedStyle(this.first, null);
                                var itemPaddingLeft = parseFloat(itemStyle.getPropertyValue('padding-left'));
                                var itemPaddingRight = parseFloat(itemStyle.getPropertyValue('padding-right')); // Zone Properties

                                this.zoneWidth = this.zoneBound.width - (centerPaddingRight + centerPaddingLeft); // Items Bound

                                this.itemBound = this.items[0].getBoundingClientRect(); // Items Properties

                                this.itemWidth = this.itemBound.width; // Calculate Maxima

                                this.max = Math.max(0, this.length * this.itemWidth - this.zoneWidth - (itemPaddingRight + itemPaddingLeft)); // Calculate Pull Values

                                this.pullMin = -1 * (this.max + this.zoneWidth / 2);
                                this.pullMax = 1 * (this.min + this.zoneWidth / 2); // Reset Translation

                            }
                        }, {
                            key: "onPrev",
                            value: function onPrev() {
                                // Update Delta
                                this.delta = -1; // Update Values
                                this.update(this.transA + this.itemWidth);
                            }
                        }, {
                            key: "onNext",
                            value: function onNext() {
                                // Update Delta
                                this.delta = 1; // Update Values
                                this.update(this.transA - this.itemWidth);
                            }
                        }, {
                            key: "update",
                            value: function update(value) {
                                var _this = this;
                                // Move
                                this.transA = this.clamp(-this.max, 0, value);
                                this.transB = this.transA; // Index
                                this.first.classList.remove('is-first');
                                this.first = this.items[this.index];
                                // this.first.classList.add('is-first'); // Clear Timeout
                                if (this.timeout) {
                                    clearTimeout(this.timeout);
                                } // Set Timeout
                                this.timeout = setTimeout(function () {
                                    return _this.zone.classList.remove('is-pressed');
                                }, 350);
                                this.inner.style.transform = "translateX(".concat(this.transB, "px)"); // Scrollbar
                                this.progress = this.clamp(0, 1, -1 * this.transB / this.max);
                                this.scrollbarHandle.style.transform = "translateX(".concat(this.progress * 100, "%)");
                                this.zone.classList.add('is-pressed');
                            }
                        }, {
                            key: "clamp",
                            value: function clamp(min, max, value) {
                                return Math.max(Math.min(value, max), min);
                            }
                        }, {
                            key: "lerp",
                            value: function lerp(a, b, t) {
                                return (1 - t) * a + t * b;
                            }
                        }]);

                    return Drag;
                }();

        if (drag) {
            // Call Drag       
            Drag = new Drag({
                el: drag
            });
        }
    });