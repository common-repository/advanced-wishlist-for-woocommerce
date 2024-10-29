export function formatString(str, args) {
    for (let key in args) {
        str = str.replace(new RegExp("\\{" + key + "\\}", "gi"), args[key]);
    }

    return str;
}
