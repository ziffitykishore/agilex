
define(['jquery'], function($){
    return {
        initSettings: function(){
            this.initSwitcher();
            this.initDefaults();
            this.initSpecialSettings();
        },
        initSwitcher: function(){
            $('.magictoolbox-switcher-status').on('change', function(event) {
                var disabled = ($(this).prop('checked') ? false : true);
                var suffix = (disabled ? 'off' : 'on');
                var label = $(this).next();
                label.attr('title', label.attr('data-text-'+suffix));
                var elements = $(this.parentNode.parentNode).prev().find('input, select');
                elements.prop('disabled', disabled);
                if (disabled) {
                    var name = elements.prop('name').replace(/(magictoolbox\[[^\]]+\])\[[^\]]+\](\[[^\]]+\])/, '$1[default]$2');
                    var value;
                    if (elements.prop('type') == 'radio') {
                        value = [$('[name=\''+name+'\']:checked').val()];
                    } else {
                        value = $('[name=\''+name+'\']').val();
                    }
                    elements.val(value);
                }
            });
        },
        initDefaults: function(){
            var profiles = [];
            $('.tabs .tab-item-link').each(function(i){
                profiles.push($(this).prop('name'));
            });
            $('#page_tabs_default_content .control').find('input, select').bind('change', function(){
                var element = $(this);
                var name = element.prop('name');
                var value = element.val();
                if (element.prop('type') == 'radio') {
                    value = [value];
                }
                for (var profileIndex in profiles) {
                    if (profiles[profileIndex] == 'default') {
                        continue;
                    }
                    var curName = name.replace(/(magictoolbox\[[^\]]+\])\[default\](\[[^\]]+\])/, '$1['+profiles[profileIndex]+']$2');
                    $('[name=\''+curName+'\']:disabled').val(value);
                }
            });
        },
        initSpecialSettings: function(){
            var profiles = [],
                names = ['zoomMode', 'zoomModeForMobile'];

            $('.tabs .tab-item-link').each(function(i){
                profiles.push($(this).prop('name'));
            });

            for (var i in profiles) {
                for (var j in names) {
                    $('[name=\'magictoolbox[desktop][' + profiles[i] + '][' + names[j] + ']\']').bind(
                        'change',
                        {
                            'profile': profiles[i],
                            'name': names[j],
                        },
                        function(event){
                            var value = $(this).val(),
                                noteId = 'desktop-' + event.data.profile + '-' + event.data.name + '-note';
                            $('#' + noteId + ' .note-alert').remove();
                            var span = document.createElement('span');
                            span.className = 'note-alert';
                            span.innerHTML = 'Caption in zoom window will not be displayed when zoom mode is off or magnifier.';
                            if (value == 'magnifier' || value == 'off') {
                                $('#' + noteId).append(span);
                            }
                        }
                    );
                }
            }
        },
        initAdvancedRadios: function(){
            $('.mt-advanced-radios input[type=radio]').bind('change', function(){
                var element = $(this);
                var id = element.attr('id');
                element.parent().parent().find('.mt-option-note').addClass('advanced-radios-hidden-note');
                $('#'+id+'-note').removeClass('advanced-radios-hidden-note');
            });
        }
    }
});
