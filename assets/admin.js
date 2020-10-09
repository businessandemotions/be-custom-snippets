"use strict";

(function(){
  var matches = Element.prototype.matches || Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
  var closest = Element.prototype.closest || function(s) {
    var el = this;
    do {
      if (matches.call(el, s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  }
  document.addEventListener('DOMContentLoaded', function() {
    var editors = document.querySelectorAll('[data-be-snippets-editor]');
    Array.prototype.forEach.call(editors, function(editor) {
      var input = editor.querySelector('[name="' + editor.dataset.beSnippetsName + '"]');
      var instance = CodeMirror(editor, {
        value: input.value,
        mode: editor.dataset.beSnippetsEditor,
        theme: "pastel-on-dark",
        lineNumbers: true,
        matchBrackets: true,
      });
      var form = closest.call(editor, 'form');
      if (form) {
        form.addEventListener('submit', function() {
          input.value = instance.getValue();
        })
      }
    });
  })
})()