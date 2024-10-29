import React, {useEffect} from 'react';
import {useTranslation} from 'react-i18next';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Box from '@mui/material/Box';
import AddToWishlistTab from "./tabs/AddToWishlistTab";
import WishlistsTab from "./tabs/WishlistsTab";
import {useDispatch, useSelector} from "react-redux";
import {getSettings, loadSettingsAsync, saveSettingsAsync} from "Store/slices/settingsSlice";
import PropTypes from "prop-types";
import {Backdrop, Button, CircularProgress} from "@mui/material";
import SystemTab from "BaseComponents/components/tabs/settings/tabs/SystemTab";

function TabPanel(props) {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`tabpanel-${index}`}
            aria-labelledby={`tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box sx={{p: 3}}>
                    {children}
                </Box>
            )}
        </div>
    );
}

TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.number.isRequired,
    value: PropTypes.number.isRequired,
};

function getTabPropsByIndex(index) {
    return {
        id: `tab-${index}`,
        'aria-controls': `tabpanel-${index}`,
    };
}

const SettingsTab = () => {
    const {t} = useTranslation();

    const [tabIndexValue, setTabIndexValue] = React.useState(0);

    const handleChange = (event, newValue) => {
        setTabIndexValue(newValue);
    };

    const dispatch = useDispatch();
    const settingsData = useSelector(getSettings);

    const settingsStatus = useSelector((state) => state.settings.status);

    useEffect(() => {
        if (settingsStatus === 'idle') {
            dispatch(loadSettingsAsync());
        }
    }, []);

    const isLoading = () => settingsStatus === 'loading';

    return (
        <Box sx={{width: '100%', position: 'relative'}}>
            <Box sx={{visibility: isLoading() ? 'hidden' : 'visible'}}>
                <Box sx={{borderBottom: 1, borderColor: 'divider'}}>
                    <Tabs value={tabIndexValue} onChange={handleChange} aria-label="tabs">
                        <Tab label={t('Add to wishlist')} {...getTabPropsByIndex(0)} />
                        <Tab label={t('Wishlists')} {...getTabPropsByIndex(1)} />
                        <Tab label={t('System')} {...getTabPropsByIndex(2)} />
                    </Tabs>
                </Box>
                <TabPanel value={tabIndexValue} index={0}><AddToWishlistTab settingsData={settingsData}/></TabPanel>
                <TabPanel value={tabIndexValue} index={1}><WishlistsTab settingsData={settingsData}/></TabPanel>
                <TabPanel value={tabIndexValue} index={2}><SystemTab settingsData={settingsData}/></TabPanel>
                <Button variant="contained" onClick={() => dispatch(saveSettingsAsync())}>{t('Save settings')}</Button>
            </Box>

            <Backdrop sx={{position: 'absolute', backgroundColor: 'inherit'}} open={isLoading()}>
                <CircularProgress/>
            </Backdrop>
        </Box>
    );
};

export default SettingsTab;
