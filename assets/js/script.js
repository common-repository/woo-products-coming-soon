jQuery(function() {
                function timer(settings) {
                    var config = {
                        timeZone: jsobj.timezone,
                        endDate: jsobj.year+' '+jsobj.month+ ' '+jsobj.day,
                        hours: jQuery('#hours'),
                        minutes: jQuery('#minutes'),
                        seconds: jQuery('#seconds'),
                        newSubMessage: 'and should be back online in a few minutes...'
                    };
                    function prependZero(number) {
                        return number < 10 ? '0' + number : number;
                    }
                    jQuery.extend(true, config, settings || {});
                    var currentTime = moment();
                    var endDate = moment.tz(config.endDate, config.timeZone);
                    var diffTime = endDate.valueOf() - currentTime.valueOf();
                    var duration = moment.duration(diffTime, 'milliseconds');
                    var days = duration.days();
                    var interval = 1000;
                    var subMessage = jQuery('.sub-message');
                    var clock = jQuery('.clock');
                    if (diffTime < 0) {
                        endEvent(subMessage, config.newSubMessage, clock);
                        return;
                    }
                    if (days > 0) {
                        jQuery('#days').text(prependZero(days));
                        jQuery('.days').css('display', 'inline-block');
                    }
                    jQuery('.hours').css('display', 'inline-block');
                    jQuery('.minutes').css('display', 'inline-block');
                    jQuery('.seconds').css('display', 'inline-block');
                    var intervalID = setInterval(function() {
                        duration = moment.duration(duration - interval, 'milliseconds');
                        var hours = duration.hours(),
                                minutes = duration.minutes(),
                                seconds = duration.seconds();
                        days = duration.days();
                        if (hours <= 0 && minutes <= 0 && seconds <= 0 && days <= 0) {
                            clearInterval(intervalID);
                            endEvent(subMessage, config.newSubMessage, clock);
                            window.location.reload();
                        }
                        if (days === 0) {
                            jQuery('.days').hide();
                        }
                        jQuery('#days').text(prependZero(days));
                        config.hours.text(prependZero(hours));
                        config.minutes.text(prependZero(minutes));
                        config.seconds.text(prependZero(seconds));
                    }, interval);
                }
                function endEvent(jQueryel, newText, hideEl) {
                    jQueryel.text(newText);
                    hideEl.hide();
                }
                timer();
            });