import React from 'react';
import {useTranslation} from 'react-i18next';
import {awlStringFormat} from "../../../../awlStringFormat";
import {renderToStaticMarkup} from "react-dom/server";

const LicenseTab = () => {
    const {t} = useTranslation();

    const siteUrl = 'https://algolplus.com';
    const siteLinkHtml = <a target='_blank' href={siteUrl}>{siteUrl}</a>;
    const accountUrl = 'https://algolplus.com/plugins/my-account';
    const accountLinkHtml = <a target="_blank" href={accountUrl}>{accountUrl}</a>;
    const dashboardLink = <a target="_blank"
                             href={algolWishlistAdminAppData.licenseTab.dashboardLink}>{t('>Dashboard > Updates')}</a>;
    const [license, setLicense] = React.useState(algolWishlistAdminAppData.licenseTab.license);
    const [licenseStatus, setLicenseStatus] = React.useState(algolWishlistAdminAppData.licenseTab.status);
    const [licenseError, setLicenseError] = React.useState(algolWishlistAdminAppData.licenseTab.error);

    const sendLicenseRequest = (e) => {
        e.preventDefault();

        const data = new FormData();
        data.append('edd_awl_nonce', algolWishlistAdminAppData.nonce);
        data.append('edd_awl_license_key', license);
        if (licenseStatus !== false && licenseStatus === 'valid') {
            data.append('action', 'awl_license_deactivate');
            data.append('edd_awl_license_deactivate', 1);
        } else {
            data.append('action', 'awl_license_activate');
            data.append('edd_awl_license_activate', 1);
        }

        const result = fetch(ajaxurl, {
            method: 'POST',
            body: data,
        }).then((response) => response.json()).catch(err => {
            console.log(err)
        });

        result.then(r => {
           console.log(r);
           setLicenseStatus(r.status);
           setLicenseError(r.error);
        });
    };

    const renderLicenseActive = () => {
        return (
            <div>
                <span style={{color: 'green'}}>{t('License is active')}</span>
                <br/>
                <br/>
                <input type="submit" className="button-secondary" name="edd_awl_license_deactivate"
                       value={t('Deactivate License')}/>
            </div>
        );
    }

    const renderLicenseInactive = () => {
        let licenseErrorMsg = '';
        if (licenseError) {
            licenseErrorMsg = <div>
                {t('License is inactive:')} <span style={{color: 'red'}}>{licenseError}</span>
            </div>
        }

        return (
            <div>
                {licenseErrorMsg}<br/>
                <input type="submit" className="button-secondary" name="edd_awl_license_activate"
                       value={t('Activate License')}/>
            </div>
        );
    }

    return (
        <div>
            <h1>{t('License')}</h1>
            <div id="license_help_text">

                <h3>{t('Licenses')}</h3>

                <div className="license_paragraph"
                     dangerouslySetInnerHTML={{
                         __html: awlStringFormat(t('The license key you received when completing your purchase from {0} will grant you access to updates until it expires.'), siteLinkHtml) +
                             renderToStaticMarkup(
                                 <br/>) + t('You do not need to enter the key below for the plugin to work, but you will need to enter it to get automatic updates.')
                     }}>
                </div>
                <div className="license_paragraph"
                     dangerouslySetInnerHTML={{
                         __html: awlStringFormat(t("If you're seeing a red message telling you that your key isn't valid or is out of installs, {0} visit {1} to manage your installs or renew / upgrade your license."),
                             <br/>, accountLinkHtml)
                     }}>
                </div>
                <div
                    className="license_paragraph"
                    dangerouslySetInnerHTML={{__html: awlStringFormat(t('Not seeing an update but expecting one? In WordPress, go to {0} and click "Check Again".'), dashboardLink)}}>
                </div>
            </div>
            <form onSubmit={sendLicenseRequest}>
                <div dangerouslySetInnerHTML={{__html: algolWishlistAdminAppData.licenseTab.settingsFields}}></div>
                <table className="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            {t('License Key')}
                        </th>
                        <td>
                            <input id="edd_awl_license_key" name="edd_awl_license_key" type="text"
                                   className="regular-text" value={license} onChange={e => setLicense(e.target.value)}/><br/>
                            <label className="description"
                                   htmlFor="edd_awl_license_key">{t('look for it inside purchase receipt (email)')}</label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                        </th>
                        <td>
                            {licenseStatus !== false && licenseStatus === 'valid'
                                ? renderLicenseActive()
                                : renderLicenseInactive()}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    );
};

export default LicenseTab;
