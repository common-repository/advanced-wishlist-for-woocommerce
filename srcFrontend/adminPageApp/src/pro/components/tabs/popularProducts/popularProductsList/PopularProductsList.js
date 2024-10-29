import React from 'react';
import {useTranslation} from 'react-i18next';
import {useDispatch, useSelector} from "react-redux";
import {
    getPopularProducts,
    getPopularProductsTotalRows,
    getPopularProductsTimeRange,
    loadPopularProductsAsync,
    getPopularProductSearchByText,
} from "Store/slices/popularProductsSlice";
import {selectedPopularProductForUsersUpdated} from "Store/slices/popularProductsControlSlice";
import {DataGrid} from '@mui/x-data-grid';
import Select from '@mui/material/Select';
import {Button, MenuItem, TextField} from "@mui/material";

import "./PopularProductsTab.css";
import {AppConfig} from "AppConfig";

import PromotionalEmailModal from './PromotionalEmailModal'

const config = new AppConfig();

const SendPromotionalEmailButton = (props) => {
    const {t} = useTranslation();

    return (
        <PromotionalEmailModal buttonTitle={t('Send promotional email')} productId={props.productId}/>
    );
};

const ViewUsersButton = (props) => {
    const {t} = useTranslation();
    const dispatch = useDispatch();

    return (
        <Button onClick={() => dispatch(selectedPopularProductForUsersUpdated({id: props.productId, name: props.name}))}
                style={{textTransform: 'none'}}>{t('View users')}</Button>
    );
};

const DownloadUserListButton = () => {
    const {t} = useTranslation();

    return (
        <Button style={{textTransform: 'none'}}>{t('Download user list')}</Button>
    );
};

const PopularProductsList = () => {
    // run search on start
    // run it runs twice, check this https://stackoverflow.com/a/66304817
    React.useEffect(() => {
        triggerSearch();
    }, [])

    const {t} = useTranslation();

    const perPage = config.getConfig().popularProductsTab.itemsPerPage;

    const data = useSelector(getPopularProducts);
    const totalRows = useSelector(getPopularProductsTotalRows);
    const dispatch = useDispatch();

    const [timeRangeValue, setTimeRangeValue] = React.useState(useSelector(getPopularProductsTimeRange));
    const [searchByTextValue, setSearchByTextValue] = React.useState(useSelector(getPopularProductSearchByText));

    const [searchLockValue, setSearchLockValue] = React.useState(false);
    const [timeOutIdValue, setTimeOutIdValue] = React.useState(null);

    const triggerSearch = (additionalArgs) => {
        const args = {
            "timeRange": timeRangeValue,
            "searchByText": searchByTextValue,
            "sorting": sortModel,
            "pagination": {
                perPage: perPage,
                currentPage: currentPage
            },
            ...additionalArgs,
        }

        setSearchLockValue(true);
        dispatch(loadPopularProductsAsync(args)).then(() => setSearchLockValue(false));
    }

    const columns = [
        {field: 'name', headerName: t('Name'), flex: 5, headerAlign: 'left',},
        {
            field: 'category',
            headerName: t('Category'),
            flex: 3,
            sortable: false,
            headerAlign: 'left',
        },
        {field: 'counter', headerName: t('Counter'), flex: 2, type: 'number', align: "left", headerAlign: 'left',},
        {
            field: 'actions',
            headerName: t('Actions'),
            sortable: false,
            type: 'actions',
            getActions: (params) => [
                <SendPromotionalEmailButton productId={params.row.id}/>,
                <ViewUsersButton productId={params.row.id} name={params.row.name}/>,
                <DownloadUserListButton/>,
            ],
            flex: 6,
            headerAlign: 'left',
        },
    ];

    const [sortModel, setSortModel] = React.useState([]);
    const [currentPage, setCurrentPage] = React.useState(1);

    return (
        <div style={{height: 400, width: '100%'}}>
            <div className={"top-menu"}>
                <Select
                    className="time-range-select"
                    value={timeRangeValue}
                    label={t('Time Range')}
                    disabled={searchLockValue}
                    readOnly={searchLockValue}
                    onChange={(event) => {
                        setTimeRangeValue(event.target.value);
                        triggerSearch({timeRange: event.target.value});
                    }}
                >
                    <MenuItem value="last_day">{t('Time Range: Last day')}</MenuItem>
                    <MenuItem value="last_week">{t('Time Range: Last week')}</MenuItem>
                    <MenuItem value="last_month">{t('Time Range: Last month')}</MenuItem>
                </Select>

                <TextField
                    className="search-product-input"
                    label={t('Search product')}
                    type="search"
                    variant="filled"
                    disabled={searchLockValue}
                    readOnly={searchLockValue}
                    onChange={(event) => {
                        setSearchByTextValue(event.target.value);

                        clearTimeout(timeOutIdValue);
                        setTimeOutIdValue(setTimeout(() => triggerSearch({searchByText: event.target.value}), 1000));
                    }}
                />
            </div>

            <DataGrid
                checkboxSelection
                rows={data}
                columns={columns}
                pageSize={perPage}
                rowsPerPageOptions={[perPage]}
                loading={searchLockValue}

                sortingMode={'server'}
                sortModel={sortModel}
                onSortModelChange={(model) => {
                    setSortModel(model);
                    triggerSearch({sorting: model});
                }}

                paginationMode={'server'}
                onPageChange={(page,) => {
                    setCurrentPage(page + 1);
                    triggerSearch({pagination: {currentPage: page + 1, perPage: perPage}});
                }}
                rowCount={totalRows}
            />
        </div>
    )
};

export default PopularProductsList;
