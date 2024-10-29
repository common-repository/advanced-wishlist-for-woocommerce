import {useTranslation} from 'react-i18next'
import React from 'react'
import {Box, Button, CircularProgress, MenuItem, Select} from '@mui/material'

const BulkActions = (props) => {
    const {bulkActions, onBulkActionApply} = props;

    const {t} = useTranslation()

    const bulkActionCallback = (e) => {
        setProcessing(true);
        onBulkActionApply(action).then(() => setProcessing(false));
    }

    const addSelectedToCartActionCallback = (e) => {
        setProcessing(true);
        onBulkActionApply('add-to-cart').then(() => setProcessing(false));
    }

    const addAllToCartActionCallback = (e) => {
        setProcessing(true);
        onBulkActionApply('add-all-to-cart').then(() => setProcessing(false));
    }

    const [action, setAction] = React.useState(bulkActions[0].value);
    const [processing, setProcessing] = React.useState(false)

    return (<Box component="span"
                 display="flex"
                 justifyContent="start"
                 alignItems="center"
                 sx={{height: 50, margin: 2, gap: 2}}
    >
        <Select
            sx={{width: 150}}
            variant="filled"
            value={action}
            label="Action"
            onChange={(e) => {
                setAction(e.target.value);
            }}
        >
            {
                bulkActions.map((bulkAction, index) => (
                    <MenuItem key={index} value={bulkAction.value}>{bulkAction.label}</MenuItem>
                ))
            }
        </Select>

        {
            processing
                ? <CircularProgress/>
                : <Button
                    sx={{width: 100, height: 1}}
                    variant="contained"
                    onClick={bulkActionCallback}
                    disableFocusRipple
                    size={'small'}
                >
                    {t('Apply')}
                </Button>
        }

        <Button
            sx={{ height: 1}}
            variant="contained"
            onClick={addSelectedToCartActionCallback}
            disableFocusRipple
            size={'small'}
        >
            {t('Add Selected To Cart')}
        </Button>
        <Button
            sx={{ height: 1}}
            variant="contained"
            onClick={addAllToCartActionCallback}
            disableFocusRipple
            size={'small'}
        >
            {t('Add All To Cart')}
        </Button>

    </Box>)
}

export default BulkActions;
