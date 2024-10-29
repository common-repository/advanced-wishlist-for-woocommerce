import React, {useState} from 'react'
import {Box, Link, Snackbar} from '@mui/material'
import {AppConfig} from "AppConfig";
import {useTranslation} from "react-i18next";

const ShareBlock = () => {
    const config = new AppConfig()
    const {t} = useTranslation()

    const [openCopiedToClipboard, setOpenCopiedToClipboard] = useState(false);
    const copyToClipboard = (text) => {
        if (window.clipboardData && window.clipboardData.setData) {
            // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
            return window.clipboardData.setData("Text", text);

        } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
                return document.execCommand("copy");  // Security exception may be thrown by some browsers.
            } catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return prompt("Copy to clipboard: Ctrl+C, Enter", text);
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }
    const handleClick = () => {
        setOpenCopiedToClipboard(true);
        copyToClipboard(config.getConfig().share.url);
    };

    let facebookShare;
    if (config.getConfig().share.facebookShareIconCustom) {
        facebookShare = <Link target="_blank" href={config.getConfig().share.facebookUrl}
                              className={"awl-share-custom"}>
            {<img src={config.getConfig().share.facebookShareIconCustom}/>}</Link>
    } else if (config.getConfig().share.facebookShareIcon) {
        facebookShare =
            <Link target="_blank" href={config.getConfig().share.facebookUrl}
                  className={"awl-share awl-facebook-link dashicons-before dashicons-" + config.getConfig().share.facebookShareIcon}>
            </Link>
    }
    let twitterShare;
    if (config.getConfig().share.twitterShareIconCustom) {
        twitterShare = <Link target="_blank" href={config.getConfig().share.twitterUrl}
                             className={"awl-share-custom"}>
            {<img src={config.getConfig().share.twitterShareIconCustom}/>}</Link>
    }
    else if (config.getConfig().share.twitterShareIcon) {
        twitterShare =
            <Link target="_blank" href={config.getConfig().share.twitterUrl}
                  className={"awl-share awl-twitter-link dashicons-before dashicons-" + config.getConfig().share.twitterShareIcon}>
            </Link>
    }
    let pinterestShare;
    if (config.getConfig().share.pinterestShareIconCustom) {
        pinterestShare = <Link target="_blank" href={config.getConfig().share.pinterestUrl}
                               className={"awl-share-custom"}>
            {<img src={config.getConfig().share.pinterestShareIconCustom}/>}</Link>
    } else if (config.getConfig().share.pinterestShareIcon) {
        pinterestShare =
            <Link target="_blank" href={config.getConfig().share.pinterestUrl}
                  className={"awl-share awl-pinterest-link dashicons-before dashicons-" + config.getConfig().share.pinterestShareIcon}>
            </Link>
    }
    let emailShare;
    if (config.getConfig().share.emailShareIconCustom) {
        emailShare = <Link target="_blank" href={config.getConfig().share.emailUrl}
                           className={"awl-share-custom"}>
            {<img src={config.getConfig().share.emailShareIconCustom}/>}</Link>
    } else if (config.getConfig().share.emailShareIcon) {
        emailShare =
            <Link target="_blank" href={config.getConfig().share.emailUrl}
                  className={"awl-share awl-email-link dashicons-before dashicons-" + config.getConfig().share.emailShareIcon}>
            </Link>
    }
    let whatsappShare;
    if (config.getConfig().share.whatsappShareIconCustom) {
        whatsappShare = <Link target="_blank" href={config.getConfig().share.whatsappUrl}
                              className={"awl-share-custom"}>
            {<img src={config.getConfig().share.whatsappShareIconCustom}/>}</Link>
    } else if (config.getConfig().share.whatsappShareIcon) {
        whatsappShare =
            <Link target="_blank" href={config.getConfig().share.whatsappUrl}
                  className={"awl-share awl-whatsapp-link dashicons-before dashicons-" + config.getConfig().share.whatsappShareIcon}>
            </Link>
    }

    return (
        <Box component="span"
             display={config.getConfig().settings.share_wishlist ? "flex" : "none"}
             justifyContent="start"
             alignItems="center"
             sx={{height: 50, margin: 2, gap: 2}}
             className="awl-share-block"
        >
            <h4>{config.getConfig().share.shareBlockTitle}</h4>

            {facebookShare}
            {twitterShare}
            {pinterestShare}
            {emailShare}
            {whatsappShare}

            <Link href="javascript:void(0)" className={"awl-share awl-share-link dashicons-before dashicons-share"}>
            </Link>

            <Snackbar
                message={t('Copied to clipboard')}
                anchorOrigin={{vertical: "top", horizontal: "center"}}
                autoHideDuration={1000}
                onClose={() => setOpenCopiedToClipboard(false)}
                open={openCopiedToClipboard}
            />
        </Box>
    )
}

export default ShareBlock
