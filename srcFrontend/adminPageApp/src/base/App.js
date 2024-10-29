import React from 'react';
import {useTranslation} from 'react-i18next';

import PropTypes from 'prop-types';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Box from '@mui/material/Box';

import {PopularProductsTab, SettingsTab, HelpTab} from "./components/tabs";

import './App.css';

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

function App() {
    const [tabIndexValue, setTabIndexValue] = React.useState(0);

    const handleChange = (event, newValue) => {
        setTabIndexValue(newValue);
    };

    const {t} = useTranslation();

    return (
        <Box sx={{width: '100%'}}>
            <Box sx={{borderBottom: 1, borderColor: 'divider'}} className={'tabs-panel'}>
                <Tabs value={tabIndexValue} onChange={handleChange} aria-label="tabs">
                    <Tab label={t('Popular Products')} {...getTabPropsByIndex(0)} />
                    <Tab label={t('Settings')} {...getTabPropsByIndex(1)} />
                    <Tab label={t('Help')} {...getTabPropsByIndex(2)} />
                </Tabs>
            </Box>
            <TabPanel value={tabIndexValue} index={0}><PopularProductsTab/></TabPanel>
            <TabPanel value={tabIndexValue} index={1}><SettingsTab/></TabPanel>
            <TabPanel value={tabIndexValue} index={2}><HelpTab/></TabPanel>
        </Box>
    );
}

export default App;
