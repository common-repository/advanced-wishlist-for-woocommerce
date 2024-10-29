import {renderToStaticMarkup} from "react-dom/server";

export function awlStringFormat(str) {
    'use strict';
    str = str.toString();
    let args = [...arguments];
    args.splice(0, 1);
    if (args.length) {
        for (let key in args) {
            //jsx element
            if (typeof args[key] === 'object') {
                args[key] = renderToStaticMarkup(args[key]);
            }
            str = str.replace(new RegExp('\\{' + key + '\\}', 'gi'), args[key]);
        }
    }

    return str;
}
