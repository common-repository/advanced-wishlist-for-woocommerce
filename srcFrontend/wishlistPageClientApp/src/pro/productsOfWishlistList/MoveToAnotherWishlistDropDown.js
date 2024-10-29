import React, {createRef} from "react";
import MoveDown from "@mui/icons-material/MoveDown";
import {GridActionsCellItem} from "@mui/x-data-grid";
import {Menu, MenuItem} from "@mui/material";
import {moveProductOfWishlistAsync} from "Store/slices/productsOfWishlistSlice";
import {useTranslation} from "react-i18next";
import {useDispatch, useSelector} from "react-redux";
import {getWishlists} from "Store/slices/wishlistsSlice";


const MoveToAnotherWishlistDropDown = (props) => {
    const rowData = props.row;
    const relationshipId = rowData.id;
    const currentWishlistId = props.currentWishlistId
    const onSelect = props.onSelect ? props.onSelect : () => void 0;

    const {t} = useTranslation();
    const dispatch = useDispatch();
    const wishlists = useSelector(getWishlists).filter((wishlist) => wishlist.id !== currentWishlistId);

    const gridActionItemRef = createRef();

    const [gridActionItemElOrNull, setGridActionItemElOrNull] = React.useState(null);
    const menuOpen = Boolean(gridActionItemElOrNull);
    const handleOpenMenu = () => {
        setGridActionItemElOrNull(gridActionItemRef.current)
    };
    const handleCloseMenu = () => {
        setGridActionItemElOrNull(null);
    };
    const handleSelectWhereToMove = (newWishlistId) => {
        return () => {
            setGridActionItemElOrNull(null);

            dispatch(moveProductOfWishlistAsync({
                wishlistId: currentWishlistId,
                relationshipId: relationshipId,
                newWishlistId: newWishlistId
            })).then(onSelect)
        }
    };

    return (
        <div>
            <GridActionsCellItem
                ref={gridActionItemRef}
                icon={<MoveDown/>}
                label={t("Move")}
                onClick={handleOpenMenu}
            />
            <Menu
                anchorEl={gridActionItemElOrNull}
                open={menuOpen}
                onClose={handleCloseMenu}
            >
                {
                    wishlists.map((wishlist) => {
                        return <MenuItem onClick={handleSelectWhereToMove(wishlist.id)}>{wishlist.title}</MenuItem>;
                    })
                }
            </Menu>
        </div>
    )
}

export default MoveToAnotherWishlistDropDown
