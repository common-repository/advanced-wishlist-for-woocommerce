import React from 'react'
import { Box, Link } from '@mui/material'
import FacebookIcon from '@mui/icons-material/Facebook';
import TwitterIcon from '@mui/icons-material/Twitter';
import PinterestIcon from '@mui/icons-material/Pinterest';
import EmailIcon from '@mui/icons-material/Email';
import WhatsAppIcon from '@mui/icons-material/WhatsApp';
import {AppConfig} from "AppConfig";

const ShareBlock = () => {
    const config = new AppConfig()

    return (
        <Box component="span"
            display={config.getConfig().settings.share_wishlist ? "flex" : "none"}
            justifyContent="start"
            alignItems="center"
            sx={{height:50, margin: 2, gap:2}}
            className="awl-share-block">
            <h4>{config.getConfig().share.shareBlockTitle}</h4>
            <Link target="_blank" href={config.getConfig().share.facebookUrl}>{config.getConfig().share.facebookShareIcon ?
                <img src={config.getConfig().share.facebookShareIcon}/> : <FacebookIcon className="awl-facebook-icon"/>}</Link>
            <Link target="_blank" href={config.getConfig().share.twitterUrl}>{config.getConfig().share.twitterShareIcon ?
                <img src={config.getConfig().share.twitterShareIcon}/> : <TwitterIcon className='awl-twitter-icon'/>}</Link>
            <Link target="_blank" href={config.getConfig().share.pinterestUrl}>{config.getConfig().share.pinterestShareIcon ?
                <img src={config.getConfig().share.pinterestShareIcon}/> : <PinterestIcon className='awl-pinterest-icon'/>}</Link>
            <Link target="_blank" href={config.getConfig().share.emailUrl}>{config.getConfig().share.emailShareIcon ?
                <img src={config.getConfig().share.emailShareIcon}/> : <EmailIcon className='awl-email-icon'/>}</Link>
            <Link target="_blank" href={config.getConfig().share.whatsappUrl}>{config.getConfig().share.whatsappShareIcon ?
                <img src={config.getConfig().share.whatsappShareIcon}/> : <WhatsAppIcon className='awl-whatsapp-icon'/>}</Link>
        </Box>
    )
}

export default ShareBlock
