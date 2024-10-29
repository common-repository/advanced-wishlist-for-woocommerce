import {useTranslation} from "react-i18next";
import {ApiClient} from "ApiClient";
import {Button} from "@mui/material";
import React from "react";

const DownloadUserListButton = (props) => {
    const {productId, variation} = props;

    const {t} = useTranslation();

    const download = async () => {
        const promise = new ApiClient().popularProductApi.adminGetUsersOfPopularItemAsFile({
            productId: productId,
            variation: variation,
        });
        promise.catch((reason) => console.log(reason));
        const response = await promise;

        // trick to download a file
        const type = response.headers['content-type']
        const blob = new Blob([response.data], {type: type, encoding: 'UTF-8'})
        const link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        var filename = "";
        var disposition = response.headers['content-disposition'];
        if (disposition && disposition.indexOf('attachment') !== -1) {
            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            var matches = filenameRegex.exec(disposition);
            if (matches != null && matches[1]) {
                filename = matches[1].replace(/['"]/g, '');
            }
        }
        link.download = filename
        link.click()
    }

    return (
        <Button onClick={download} style={{textTransform: 'none'}}>{t('Download user list')}</Button>
    );
};

export default DownloadUserListButton;
