/* eslint-disable */
const _slicedToArray = (function () {
  function sliceIterator(arr, i) {
    const _arr = [];
    let _n = true;
    let _d = false;
    let _e = undefined;
    try {
      for (
        var _i = arr[Symbol.iterator](), _s;
        !(_n = (_s = _i.next()).done);
        _n = true
      ) {
        _arr.push(_s.value);

        if (i && _arr.length === i) break;
      }
    } catch (err) {
      _d = true;
      _e = err;
    } finally {
      try {
        if (!_n && _i['return']) _i['return']();
      } finally {
        if (_d) throw _e;
      }
    }

    return _arr;
  }

  return function (arr, i) {
    if (Array.isArray(arr)) {
      return arr;
    } else if (Symbol.iterator in Object(arr)) {
      return sliceIterator(arr, i);
    } else {
      throw new TypeError(
        'Invalid attempt to destructure non-iterable instance'
      );
    }
  };
})();

export default function inlineStyleToObject(inlineStyle) {
  // just return empty object if the inlineStyle is empty
  if (!inlineStyle) {
    return {};
  }

  return inlineStyle.split(';').reduce((styleObject, stylePropertyValue) => {
    // extract the style property name and value
    const _stylePropertyValue$s = stylePropertyValue
      .split(/^([^:]+):/)
      .filter((val, i) => {
        return i > 0;
      })
      .map(item => {
        return item.trim().toLowerCase();
      });
    const _stylePropertyValue$s2 = _slicedToArray(_stylePropertyValue$s, 2);
    let property = _stylePropertyValue$s2[0];
    const value = _stylePropertyValue$s2[1];

    // if there is no value (i.e. no : in the style) then ignore it

    if (value === undefined) {
      return styleObject;
    }

    // convert the property name into the correct React format
    // remove all hyphens and convert the letter immediately after each hyphen to upper case
    // additionally don't uppercase any -ms- prefix
    // e.g. -ms-style-property = msStyleProperty
    //      -webkit-style-property = WebkitStyleProperty
    property = property
      .replace(/^-ms-/, 'ms-')
      .replace(/-(.)/g, (_, character) => {
        return character.toUpperCase();
      });

    // add the new style property and value to the style object
    styleObject[property] = value;

    return styleObject;
  }, {});
}
