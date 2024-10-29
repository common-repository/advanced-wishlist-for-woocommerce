export function getRefreshedFragments() {
    let event = document.createEvent("Event");
    event.initEvent("wc_fragment_refresh", false, true);
    document.body.dispatchEvent(event);
}
