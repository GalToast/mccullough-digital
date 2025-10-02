/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./blocks/button/block.json":
/*!**********************************!*\
  !*** ./blocks/button/block.json ***!
  \**********************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"name":"mccullough-digital/button","title":"Neon Button","category":"mcd-blocks","icon":"button","description":"A single call-to-action button that reuses the hero gradient sweep styling.","keywords":["button","cta","link"],"apiVersion":2,"supports":{"html":false,"anchor":true,"align":["left","center","right"],"spacing":{"margin":true,"padding":true}},"attributes":{"buttonText":{"type":"string","default":"Start a Project"},"buttonLink":{"type":"string","default":""},"opensInNewTab":{"type":"boolean","default":false},"align":{"type":"string","default":"none"}},"editorScript":"file:../../build/blocks/button/editor.js","style":"file:./style.css","render":"file:./render.php","textdomain":"mccullough-digital"}');

/***/ }),

/***/ "./blocks/button/editor.js":
/*!*********************************!*\
  !*** ./blocks/button/editor.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ \"@wordpress/blocks\");\n/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ \"@wordpress/block-editor\");\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./block.json */ \"./blocks/button/block.json\");\n\n\n\n\n\n\n(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_5__.name, {\n  ..._block_json__WEBPACK_IMPORTED_MODULE_5__,\n  edit({\n    attributes,\n    setAttributes\n  }) {\n    const {\n      buttonText,\n      buttonLink,\n      opensInNewTab\n    } = attributes;\n    const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.useBlockProps)({\n      className: 'mcd-button-block'\n    });\n    const buttonBaseClass = 'cta-button hero__cta-button';\n    const commonButtonProps = {\n      className: buttonBaseClass\n    };\n    if (buttonLink) {\n      commonButtonProps.href = buttonLink;\n      if (opensInNewTab) {\n        commonButtonProps.target = '_blank';\n        commonButtonProps.rel = 'noopener';\n      }\n    }\n    const ButtonTag = buttonLink ? 'a' : 'button';\n    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.BlockControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToolbarGroup, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.URLInputButton, {\n      url: buttonLink,\n      onChange: url => setAttributes({\n        buttonLink: url !== null && url !== void 0 ? url : ''\n      })\n    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {\n      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Button Settings', 'mccullough-digital')\n    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {\n      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Open in new tab', 'mccullough-digital'),\n      checked: opensInNewTab,\n      onChange: value => setAttributes({\n        opensInNewTab: value\n      }),\n      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Adds target=\"_blank\" and rel=\"noopener\" when a URL is set.', 'mccullough-digital'),\n      disabled: !buttonLink\n    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(\"div\", {\n      ...blockProps\n    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ButtonTag, {\n      ...commonButtonProps,\n      type: buttonLink ? undefined : 'button'\n    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.RichText, {\n      tagName: \"span\",\n      className: \"hero__cta-button-label\",\n      value: buttonText,\n      onChange: value => setAttributes({\n        buttonText: value\n      }),\n      allowedFormats: [],\n      placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Add button text…', 'mccullough-digital')\n    })), !buttonLink && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Notice, {\n      status: \"info\",\n      isDismissible: false,\n      className: \"mcd-button-block__link-notice\"\n    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Add a link from the toolbar to output an anchor element on the front end.', 'mccullough-digital'))));\n  },\n  save() {\n    return null;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,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O0VBRVgsQ0FBQztFQUNEMkMsSUFBSUEsQ0FBQSxFQUFHO0lBQ0gsT0FBTyxJQUFJO0VBQ2Y7QUFDSixDQUFDLENBQUMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9tY2N1bGxvdWdoLWRpZ2l0YWwtdGhlbWUvLi9ibG9ja3MvYnV0dG9uL2VkaXRvci5qcz83YjY0Il0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCB7IHJlZ2lzdGVyQmxvY2tUeXBlIH0gZnJvbSAnQHdvcmRwcmVzcy9ibG9ja3MnO1xuaW1wb3J0IHsgX18gfSBmcm9tICdAd29yZHByZXNzL2kxOG4nO1xuaW1wb3J0IHtcbiAgICB1c2VCbG9ja1Byb3BzLFxuICAgIFJpY2hUZXh0LFxuICAgIEJsb2NrQ29udHJvbHMsXG4gICAgVVJMSW5wdXRCdXR0b24sXG4gICAgSW5zcGVjdG9yQ29udHJvbHMsXG59IGZyb20gJ0B3b3JkcHJlc3MvYmxvY2stZWRpdG9yJztcbmltcG9ydCB7XG4gICAgUGFuZWxCb2R5LFxuICAgIFRvZ2dsZUNvbnRyb2wsXG4gICAgTm90aWNlLFxuICAgIFRvb2xiYXJHcm91cCxcbn0gZnJvbSAnQHdvcmRwcmVzcy9jb21wb25lbnRzJztcblxuaW1wb3J0IG1ldGFkYXRhIGZyb20gJy4vYmxvY2suanNvbic7XG5cbnJlZ2lzdGVyQmxvY2tUeXBlKCBtZXRhZGF0YS5uYW1lLCB7XG4gICAgLi4ubWV0YWRhdGEsXG4gICAgZWRpdCggeyBhdHRyaWJ1dGVzLCBzZXRBdHRyaWJ1dGVzIH0gKSB7XG4gICAgICAgIGNvbnN0IHsgYnV0dG9uVGV4dCwgYnV0dG9uTGluaywgb3BlbnNJbk5ld1RhYiB9ID0gYXR0cmlidXRlcztcbiAgICAgICAgY29uc3QgYmxvY2tQcm9wcyA9IHVzZUJsb2NrUHJvcHMoIHtcbiAgICAgICAgICAgIGNsYXNzTmFtZTogJ21jZC1idXR0b24tYmxvY2snLFxuICAgICAgICB9ICk7XG5cbiAgICAgICAgY29uc3QgYnV0dG9uQmFzZUNsYXNzID0gJ2N0YS1idXR0b24gaGVyb19fY3RhLWJ1dHRvbic7XG4gICAgICAgIGNvbnN0IGNvbW1vbkJ1dHRvblByb3BzID0ge1xuICAgICAgICAgICAgY2xhc3NOYW1lOiBidXR0b25CYXNlQ2xhc3MsXG4gICAgICAgIH07XG5cbiAgICAgICAgaWYgKCBidXR0b25MaW5rICkge1xuICAgICAgICAgICAgY29tbW9uQnV0dG9uUHJvcHMuaHJlZiA9IGJ1dHRvbkxpbms7XG4gICAgICAgICAgICBpZiAoIG9wZW5zSW5OZXdUYWIgKSB7XG4gICAgICAgICAgICAgICAgY29tbW9uQnV0dG9uUHJvcHMudGFyZ2V0ID0gJ19ibGFuayc7XG4gICAgICAgICAgICAgICAgY29tbW9uQnV0dG9uUHJvcHMucmVsID0gJ25vb3BlbmVyJztcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IEJ1dHRvblRhZyA9IGJ1dHRvbkxpbmsgPyAnYScgOiAnYnV0dG9uJztcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPD5cbiAgICAgICAgICAgICAgICA8QmxvY2tDb250cm9scz5cbiAgICAgICAgICAgICAgICAgICAgPFRvb2xiYXJHcm91cD5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxVUkxJbnB1dEJ1dHRvblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybD17IGJ1dHRvbkxpbmsgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgKCB1cmwgKSA9PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZXRBdHRyaWJ1dGVzKCB7IGJ1dHRvbkxpbms6IHVybCA/PyAnJyB9IClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8L1Rvb2xiYXJHcm91cD5cbiAgICAgICAgICAgICAgICA8L0Jsb2NrQ29udHJvbHM+XG4gICAgICAgICAgICAgICAgPEluc3BlY3RvckNvbnRyb2xzPlxuICAgICAgICAgICAgICAgICAgICA8UGFuZWxCb2R5IHRpdGxlPXsgX18oJ0J1dHRvbiBTZXR0aW5ncycsICdtY2N1bGxvdWdoLWRpZ2l0YWwnKSB9PlxuICAgICAgICAgICAgICAgICAgICAgICAgPFRvZ2dsZUNvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsYWJlbD17IF9fKCdPcGVuIGluIG5ldyB0YWInLCAnbWNjdWxsb3VnaC1kaWdpdGFsJykgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNoZWNrZWQ9eyBvcGVuc0luTmV3VGFiIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17ICh2YWx1ZSkgPT4gc2V0QXR0cmlidXRlcyh7IG9wZW5zSW5OZXdUYWI6IHZhbHVlIH0pIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBoZWxwPXsgX18oJ0FkZHMgdGFyZ2V0PVwiX2JsYW5rXCIgYW5kIHJlbD1cIm5vb3BlbmVyXCIgd2hlbiBhIFVSTCBpcyBzZXQuJywgJ21jY3VsbG91Z2gtZGlnaXRhbCcpIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkaXNhYmxlZD17ICEgYnV0dG9uTGluayB9XG4gICAgICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8L1BhbmVsQm9keT5cbiAgICAgICAgICAgICAgICA8L0luc3BlY3RvckNvbnRyb2xzPlxuICAgICAgICAgICAgICAgIDxkaXYgeyAuLi5ibG9ja1Byb3BzIH0+XG4gICAgICAgICAgICAgICAgICAgIDxCdXR0b25UYWcgeyAuLi5jb21tb25CdXR0b25Qcm9wcyB9IHR5cGU9eyBidXR0b25MaW5rID8gdW5kZWZpbmVkIDogJ2J1dHRvbicgfT5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxSaWNoVGV4dFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRhZ05hbWU9XCJzcGFuXCJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGFzc05hbWU9XCJoZXJvX19jdGEtYnV0dG9uLWxhYmVsXCJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGJ1dHRvblRleHQgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgKHZhbHVlKSA9PiBzZXRBdHRyaWJ1dGVzKHsgYnV0dG9uVGV4dDogdmFsdWUgfSkgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsbG93ZWRGb3JtYXRzPXsgW10gfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBsYWNlaG9sZGVyPXsgX18oJ0FkZCBidXR0b24gdGV4dOKApicsICdtY2N1bGxvdWdoLWRpZ2l0YWwnKSB9XG4gICAgICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8L0J1dHRvblRhZz5cbiAgICAgICAgICAgICAgICAgICAgeyAhIGJ1dHRvbkxpbmsgJiYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgPE5vdGljZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YXR1cz1cImluZm9cIlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzRGlzbWlzc2libGU9eyBmYWxzZSB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3NOYW1lPVwibWNkLWJ1dHRvbi1ibG9ja19fbGluay1ub3RpY2VcIlxuICAgICAgICAgICAgICAgICAgICAgICAgPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHsgX18oJ0FkZCBhIGxpbmsgZnJvbSB0aGUgdG9vbGJhciB0byBvdXRwdXQgYW4gYW5jaG9yIGVsZW1lbnQgb24gdGhlIGZyb250IGVuZC4nLCAnbWNjdWxsb3VnaC1kaWdpdGFsJykgfVxuICAgICAgICAgICAgICAgICAgICAgICAgPC9Ob3RpY2U+XG4gICAgICAgICAgICAgICAgICAgICkgfVxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC8+XG4gICAgICAgICk7XG4gICAgfSxcbiAgICBzYXZlKCkge1xuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9LFxufSk7XG4iXSwibmFtZXMiOlsicmVnaXN0ZXJCbG9ja1R5cGUiLCJfXyIsInVzZUJsb2NrUHJvcHMiLCJSaWNoVGV4dCIsIkJsb2NrQ29udHJvbHMiLCJVUkxJbnB1dEJ1dHRvbiIsIkluc3BlY3RvckNvbnRyb2xzIiwiUGFuZWxCb2R5IiwiVG9nZ2xlQ29udHJvbCIsIk5vdGljZSIsIlRvb2xiYXJHcm91cCIsIm1ldGFkYXRhIiwibmFtZSIsImVkaXQiLCJhdHRyaWJ1dGVzIiwic2V0QXR0cmlidXRlcyIsImJ1dHRvblRleHQiLCJidXR0b25MaW5rIiwib3BlbnNJbk5ld1RhYiIsImJsb2NrUHJvcHMiLCJjbGFzc05hbWUiLCJidXR0b25CYXNlQ2xhc3MiLCJjb21tb25CdXR0b25Qcm9wcyIsImhyZWYiLCJ0YXJnZXQiLCJyZWwiLCJCdXR0b25UYWciLCJjcmVhdGVFbGVtZW50IiwiRnJhZ21lbnQiLCJ1cmwiLCJvbkNoYW5nZSIsInRpdGxlIiwibGFiZWwiLCJjaGVja2VkIiwidmFsdWUiLCJoZWxwIiwiZGlzYWJsZWQiLCJ0eXBlIiwidW5kZWZpbmVkIiwidGFnTmFtZSIsImFsbG93ZWRGb3JtYXRzIiwicGxhY2Vob2xkZXIiLCJzdGF0dXMiLCJpc0Rpc21pc3NpYmxlIiwic2F2ZSJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./blocks/button/editor.js\n\n}");

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/
/************************************************************************/
/******/
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./blocks/button/editor.js");
/******/
/******/ })()
;