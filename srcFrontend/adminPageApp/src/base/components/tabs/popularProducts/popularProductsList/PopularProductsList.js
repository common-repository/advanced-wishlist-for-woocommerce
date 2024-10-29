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
import {DataGrid} from '@mui/x-data-grid';
import Select from '@mui/material/Select';
import {Button, MenuItem, TextField} from "@mui/material";

import "./PopularProductsTab.css";
import {AppConfig} from "AppConfig";
import DownloadUserListButton from "./DownloadUserListButton";

const config = new AppConfig();

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

    const nameLink = (params) => {
        return <a href={params.row.link} target="_blank">{params.row.name}</a>
    }

    const columns = [
        {field: 'nameLink', headerName: t('Name'), flex: 5, headerAlign: 'left', renderCell: nameLink},
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
                <DownloadUserListButton productId={params.row.id} variation={params.row.variation}/>,
            ],
            flex: 6,
            headerAlign: 'left',
        },
    ];

    const [sortModel, setSortModel] = React.useState([]);
    const [currentPage, setCurrentPage] = React.useState(1);

    return (
        <div style={{height: 800, width: '100%'}}>
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
                    <MenuItem value="all_time">{t('Time Range: All time')}</MenuItem>
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
