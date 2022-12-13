const shareHost = function (link) {
    let url = process.env.VUE_APP_SHARE_HOST;

    if (link) {
        url += link;
    }

    return url;
}

export {
    shareHost
}
