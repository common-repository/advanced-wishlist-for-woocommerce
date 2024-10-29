import React from 'react';
import {useTranslation} from 'react-i18next';
import {useDispatch, useSelector} from "react-redux";
import {
    getUsersOfPopularProduct,
    getUsersOfPopularProductTotalRows,
    loadUsersOfPopularProductAsync
} from "Store/slices/usersOfPopularProductSlice";
import {DataGrid} from '@mui/x-data-grid';
import {Button, MenuItem, TextField} from "@mui/material";

import {AppConfig} from "AppConfig";
import moment from "moment";

import PromotionalEmailModal from './../popularProductsList/PromotionalEmailModal'

const config = new AppConfig();

const CreatePromotion = (props) => {
    const {t} = useTranslation();

    return (
        <PromotionalEmailModal buttonTitle={t('Create promotion')} productId={props.productId} userId={props.userId}/>
    );
};

const PopularProductsTab = (props) => {
    // run search on start
    // run it runs twice, check this https://stackoverflow.com/a/66304817
    React.useEffect(() => {
        triggerSearch();
    }, [])

    const productId = props.product.id;
    const productName = props.product.name;

    const {t} = useTranslation();

    const perPage = config.getConfig().popularProductsTab.itemsPerPage;

    const data = useSelector(getUsersOfPopularProduct);
    const totalRows = useSelector(getUsersOfPopularProductTotalRows);
    const dispatch = useDispatch();

    const [searchLockValue, setSearchLockValue] = React.useState(false);

    const triggerSearch = (additionalArgs) => {
        const args = {
            "productId": productId,
            "sorting": sortModel,
            "pagination": {
                perPage: perPage,
                currentPage: currentPage
            },
            ...additionalArgs,
        }

        setSearchLockValue(true);
        dispatch(loadUsersOfPopularProductAsync(args)).then(() => setSearchLockValue(false));
    }

    const columns = [
        {
            field: 'thumbnailUrl',
            headerName: t('Icon'),
            flex: 1,
            headerAlign: 'left',
            sortable: false,
            renderCell: (params) => <div dangerouslySetInnerHTML={{__html: params.value}}></div>,
        },
        {
            field: 'name',
            headerName: t('Name'),
            flex: 2,
            headerAlign: 'left',
            sortable: false,
        },
        {
            field: 'addedOn',
            headerName: t('Added on'),
            flex: 2,
            sortable: true,
            headerAlign: 'left',
            type: 'dateTime',
            valueGetter: ({value}) => value && moment.tz(value.date, value.timezone).tz(moment.tz.guess()),
            valueFormatter: (params) => {
                return params.value.format("DD MMM YYYY");
            },
        },
        {
            field: 'actions',
            headerName: t('Actions'),
            sortable: false,
            type: 'actions',
            renderCell: (params) => <div><CreatePromotion productId={productId} userId={params.row.id}/></div>,
            flex: 3,
            headerAlign: 'left',
        },
    ];

    const [sortModel, setSortModel] = React.useState([]);
    const [currentPage, setCurrentPage] = React.useState(1);

    return (
        <div style={{height: 400, width: '100%'}}>
            <p>{t("Users that added ") + `"${productName}"` + t(" to wishlist")}</p>

            <DataGrid
                rows={data}
                columns={columns}
                pageSize={perPage}
                rowsPerPageOptions={[perPage]}
                checkboxSelection
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
    );
};

export default PopularProductsTab;
