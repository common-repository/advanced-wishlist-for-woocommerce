import React from 'react';
import {useTranslation} from 'react-i18next';
import {useDispatch, useSelector} from "react-redux";
import {
    getSelectedPopularProductForUsers,
    selectedPopularProductForUsersDrop
} from "Store/slices/popularProductsControlSlice";
import {Link} from "@mui/material";

import Users from "./usersOfPopularProductList/UsersOfPopularProductList";
import PopularProductsList from "./popularProductsList/PopularProductsList";

const PopularProductsTab = () => {
    const {t} = useTranslation();

    const dispatch = useDispatch();

    let selectedPopularProductForUsers = useSelector(getSelectedPopularProductForUsers);

    if (selectedPopularProductForUsers.id) {
        return (
            <div>
                <div style={{margin: '10px 0'}}>
                    <Link href="#"
                          onClick={() => dispatch(selectedPopularProductForUsersDrop())}>{t("<< Back to popular")}</Link>
                </div>
                <Users product={selectedPopularProductForUsers}/>
            </div>
        )
    } else {
        return (<div><PopularProductsList/></div>)
    }
};

export default PopularProductsTab;
