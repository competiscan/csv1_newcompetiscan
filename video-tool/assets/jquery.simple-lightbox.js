/* --------------------------------------------------------
Code for jquery.simpleLightbox()

version: 0.0.1
author: Thomas Schwarz
email: info@thomasschwarz-websolutions.de
website: https://www.thomasschwarz-websolutions.de
License: MIT
  _____ _                      ___     _                       ___  __ 
 |_   _| |_  ___ _ __  __ _ __/ __| __| |___ __ ____ _ _ _ ___/ _ \/ / 
   | | | ' \/ _ \ '  \/ _` (_-<__ \/ _| ' \ V  V / _` | '_|_ /\_, / _ \
   |_| |_||_\___/_|_|_\__,_/__/___/\__|_||_\_/\_/\__,_|_| /__| /_/\___/
----------------------------------------------------------*/
(function ( $ ) {

    /*
    * _removeLightbox
    * Remove all open lightbox's.
    */
    var _removeLightbox = function(event) {

      // Check if we have an event.
      if (typeof event !== 'undefined') {

        // Get class name of clicked element.
        var className = event.target.className;

        // Check for clicked target.
        if (className == 'sl-wrapper' || className == 'sl-close') {

          // Get all elements.
          var elements = document.getElementsByClassName('sl-wrapper');

          // Check if we had elements.
          if (elements.length) {

            // Remove all.
            while(elements.length > 0) {
              elements[0].remove();
            }
          }

        }
      }
    }

    /*
    * _createStructure
    * Creates new HTML structure for the lightbox.
    */
    var _createStructure = function(link) {

      // Create new element and structure.
      var lightbox = document.createElement('div'),
          structure =
          '<div class="sl-content">' +
            '<span class="sl-close"></span>' +
            '<img src="' + link + '" alt="Simple Lightbox" />' +
          '</div>';

      // Set class for new element.
      lightbox.className = 'sl-wrapper';

      // Append structure for content.
      lightbox.innerHTML = structure;

      return lightbox;
    }

    /*
    * _openLightbox
    * Create and open the simple lightbox.
    */
    var _openLightbox = function(element, options) {

      // Remove all elements.
      _removeLightbox();

      // Create new lightbox.
      // -> get source of clicked element.
      var lightbox = _createStructure(element.getAttribute(options.source));

      // Append new lightbox to document.
      document.body.appendChild(lightbox);

      // Add "close" event listener on click.
      document.querySelector('.sl-wrapper')
      .addEventListener('click', _removeLightbox, false);
    }

    /*
    * simpleLightbox
    * plugin definition.
    */
    $.fn.simpleLightbox = function( options ) {

        // Set default options.
        var settings = $.extend({
            source: 'src',
            trigger: 'img'
        }, options );

        // Get all trigger elements by options.trigger.
        var triggerElements = document.querySelectorAll(settings.trigger);

        // Add event listener on trigger elements for click.
        for (var i = 0; i < triggerElements.length; i++) {
          triggerElements[i].addEventListener('click', function(element) {
             _openLightbox(element.target, settings);
           });
        }

        return this;
    };

}( jQuery ));
