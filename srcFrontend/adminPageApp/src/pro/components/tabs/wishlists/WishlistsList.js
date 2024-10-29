import React from "react";
import {
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
    MenuItem,
    TextField
} from "@mui/material";
import {DataGrid} from "@mui/x-data-grid";
import {useTranslation} from "react-i18next";
import {useDispatch, useSelector} from "react-redux";
import {
    getWishlists,
    getWishlistsTotalRows,
    getWishlistsSearchByText,
    loadWishlistsAsync,
    deleteWishlistsAsync
} from "Store/slices/wishlistsSlice";
import {AppConfig} from "AppConfig";
import moment from "moment/moment";
import momentTimezone from "moment-timezone"; // do not remove!
import "./WishlistsList.css";
import Select from "@mui/material/Select";

const config = new AppConfig();

const WishlistsList = () => {
    // run search on start
    // run it runs twice, check this https://stackoverflow.com/a/66304817
    React.useEffect(() => {
        triggerSearch();
    }, [])

    const {t} = useTranslation();

    const perPage = config.getConfig().wishlistsTab.itemsPerPage;

    const data = useSelector(getWishlists);
    const totalRows = useSelector(getWishlistsTotalRows);
    const dispatch = useDispatch();

    const [bulkActionValue, setBulkActionValue] = React.useState("empty");
    const [searchByTextValue, setSearchByTextValue] = React.useState(useSelector(getWishlistsSearchByText));

    const [searchLockValue, setSearchLockValue] = React.useState(false);
    const [timeOutIdValue, setTimeOutIdValue] = React.useState(null);

    const triggerSearch = (additionalArgs) => {
        const args = {
            "searchByText": searchByTextValue,
            "sorting": sortModel,
            "pagination": {
                perPage: perPage,
                currentPage: currentPage
            },
            ...additionalArgs,
        }

        setSearchLockValue(true);
        dispatch(loadWishlistsAsync(args)).then(() => setSearchLockValue(false));
    }

    const triggerBulkAction = () => {
        switch (bulkActionValue) {
            case "delete":
                if (!confirm(t("Are you sure?"))) {
                    return;
                }

                const args = {
                    "wishlistIds": selectionModel,
                }

                setSearchLockValue(true);
                dispatch(deleteWishlistsAsync(args)).then(() => triggerSearch());
                break;
            default:
                break;
        }

        setSelectionModel([]);
    }

    const shareTypeLabel = (shareType) => {
        switch (shareType) {
            case 0:
                return t('Public');
            case 1:
                return t('Private');
            default:
                return t('Unknown');
        }
    }

    const columns = [
        {
            field: 'name',
            headerName: t('Title'),
            flex: 1,
            headerAlign: 'left',
            sortable: true,
        },
        {
            field: 'username',
            headerName: t('Usermane'),
            flex: 1,
            sortable: true,
            headerAlign: 'left',
        },
        {
            field: 'shareType',
            headerName: t('Privacy'),
            flex: 1,
            align: "left",
            headerAlign: 'left',
            sortable: true,
            valueFormatter: (params) => {
                return shareTypeLabel(params.value);
            },
        },
        {
            field: 'itemsCount',
            headerName: t('Items in list'),
            sortable: true,
            flex: 1,
            headerAlign: 'left',
        },
        {
            field: 'dateCreated',
            headerName: t('Date created'),
            sortable: true,
            flex: 1,
            headerAlign: 'left',
            type: 'dateTime',
            valueGetter: ({value}) => value && moment.tz(value.date, value.timezone).tz(moment.tz.guess()),
            valueFormatter: (params) => {
                return params.value.format("DD MMM YYYY");
            },
        },
    ];

    const [sortModel, setSortModel] = React.useState([]);
    const [currentPage, setCurrentPage] = React.useState(1);
    const [selectionModel, setSelectionModel] = React.useState([]);

    return (
        <div style={{height: 400, width: '100%'}}>
            <div className={"top-menu"}>
                <div>
                    <Select
                        style={{width: '150px'}}
                        className="bulk-action-select"
                        value={bulkActionValue}
                        label={t('Bulk action')}
                        disabled={searchLockValue}
                        readOnly={searchLockValue}
                        onChange={(event) => {
                            setBulkActionValue(event.target.value);
                        }}
                    >
                        <MenuItem value="empty">{t('Bulk actions')}</MenuItem>
                        <MenuItem value="delete">{t('Delete')}</MenuItem>
                    </Select>
                    <Button onClick={triggerBulkAction}>{t('Apply')}</Button>
                </div>

                <TextField
                    className="search-wishlist-input"
                    label={t('Search wishlist')}
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

                onSelectionModelChange={(newSelectionModel) => {
                    setSelectionModel(newSelectionModel);
                }}
                selectionModel={selectionModel}
            />
        </div>
    )

};

export default WishlistsList;
