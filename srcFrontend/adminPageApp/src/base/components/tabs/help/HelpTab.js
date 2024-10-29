import React from 'react';
import {useTranslation} from 'react-i18next';
import {awlStringFormat} from "../../../../awlStringFormat";

const HelpTab = () => {
    const {t} = useTranslation();
    const submitTicketLink = <a target='_blank' href="https://algolplus.freshdesk.com/support/tickets/new">{t('Submit a new ticket')}</a>

    return (
        <div>
            <h1>{t('Help')}</h1>
            <div dangerouslySetInnerHTML={{__html: awlStringFormat(t('Need help? Create a new theme in a WordPress support forum or {0} to the helpdesk'),
                    submitTicketLink)}}>
            </div>
        </div>
    );
};

export default HelpTab;
