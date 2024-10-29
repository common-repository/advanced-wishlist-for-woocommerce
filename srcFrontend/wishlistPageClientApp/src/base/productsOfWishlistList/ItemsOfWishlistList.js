import React from 'react'
import { GridActionsCellItem } from '@mui/x-data-grid'
import { useTranslation } from 'react-i18next'
import { useDispatch, useSelector } from 'react-redux'
import {
    getProductsOfWishlist,
    loadProductsOfWishlistAsync,
    commitNewOrdinationProductOfWishlistAsync,
    setList, deleteProductOfWishlistAsync,
    removeRowFromList,
    batchAddToCartItemsOfWishlistAsync,
    batchDeleteItemsOfWishlistAsync,
    addToCartAllItemsOfWishlistAsync
} from 'Store/slices/productsOfWishlistSlice'
import {
    Avatar,
    Backdrop,
    Box,
    Card, Checkbox,
    Link,
    List,
    ListItem,
    ListItemAvatar,
    ListItemIcon,
    ListItemText, Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    useMediaQuery,
    useTheme,
} from '@mui/material'
import DeleteIcon from '@mui/icons-material/Delete'
import { DragDropContext, Draggable, Droppable } from 'react-beautiful-dnd'
import WoocommerceStockStatus from 'BaseComponents/productsOfWishlistList/WoocommerceStockStatus'
import AddToCartButton from 'BaseComponents/productsOfWishlistList/AddToCartButton'
import BulkActions from 'BaseComponents/productsOfWishlistList/BulkActions'
import {AppConfig} from "AppConfig";
import {getRefreshedFragments} from "Utils/cartFragments";
import {formatString} from "Utils/formatString";
import ShareBlock from "BaseComponents/productsOfWishlistList/ShareBlock";
import {NotificationsBlock, showSuccessMessage, showErrorMessage} from "BaseComponents/productsOfWishlistList/NotificationsBlock";

const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list)
    const [removed] = result.splice(startIndex, 1)
    result.splice(endIndex, 0, removed)

    return result
}

const ItemsOfWishlistList = (props) => {
    // run search on start
    // run it runs twice, check this https://stackoverflow.com/a/66304817
    React.useEffect(() => {
        lockInterfaceUntilPromiseIsDone(triggerFetch())
    }, [])

    const wishlistId = parseInt(props.wishlistId, 10)

    const mutable = !!props.mutable

    const dispatch = useDispatch()
    const { t } = useTranslation()
    const config = new AppConfig()

    const rows = useSelector(getProductsOfWishlist)
    const [loading, setLoading] = React.useState(false)
    const lockInterfaceUntilPromiseIsDone = (promise) => {
        setLoading(true)
        return promise.then(() => setLoading(false))
    }

    const triggerFetch = (additionalArgs) => {
        let args = {
            'wishlistId': wishlistId,
        }

        return dispatch(loadProductsOfWishlistAsync({ ...args, ...additionalArgs }))
    }

    const [checked, setChecked] = React.useState([])

    const handleToggle = (value) => () => {
        const currentIndex = checked.indexOf(value)
        const newChecked = [...checked]

        if (currentIndex === -1) {
            newChecked.push(value)
        } else {
            newChecked.splice(currentIndex, 1)
        }

        setChecked(newChecked)
    }

    const handleToggleAll = () => {
        if (checked.length > 0) {
            setChecked([])
        } else {
            setChecked(rows.map((row) => row.id))
        }
    }

    const invokeRemoveRowCallback = (row) => () => {
        lockInterfaceUntilPromiseIsDone(dispatch(deleteProductOfWishlistAsync({
            wishlistId: wishlistId,
            relationshipId: row.id
        }))
            .then((result) => {
                !result.error && dispatch(removeRowFromList(row))

                return result
            }).then((result) => {
                if ( !result.error ) {
                    showSuccessMessage(
                        formatString(t('Item "{title}" is successfully deleted'), {title: row.title})
                    );
                } else {
                    showErrorMessage(
                        formatString(t('Item "{title}" was not deleted'), {title: row.title})
                    );
                }

                return result
            })
        )

    }

    const showColumns = config.getConfig().columns;

    const columns = [
        {
            id: 'bulkSelectCheckbox',
            label: t('Icon'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => <Checkbox
                onClick={handleToggle(row.id)}
                edge="start"
                checked={checked.indexOf(row.id) !== -1}
                tabIndex={-1}
                disableRipple
            />,
            renderColumn: () => rows.length > 0 && <Checkbox
                onClick={handleToggleAll}
                edge="start"
                checked={checked.length > 0 && checked.length === rows.length}
                tabIndex={-1}
                disableRipple
            />,
            visible: true,
        },
        {
            id: 'deleteControl',
            label: '',
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => <GridActionsCellItem onClick={invokeRemoveRowCallback(row)} icon={<DeleteIcon/>}
                                                      label="Delete"/>,
            visible: mutable,
        },
        {
            id: 'thumbnail',
            label: t('Image'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => <Avatar
                variant="square"
                src={row.thumbnail.previewUrl}
                sx={{ width: 50, height: 50 }}
                alt={'preview'}
            />,
            visible: showColumns.icon,
        },
        {
            id: 'title',
            label: t('Name'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => row.permalink ? <Link href={row.permalink} target="_blank">{row.title}</Link> : <>{row.title}</>,
            visible: true,
        },
        {
            id: 'unitPrice',
            label: t('Unit Price'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => <span dangerouslySetInnerHTML={{__html:row.price.priceHtml}}></span>,
            visible: showColumns.price,
        },
        {
            id: 'stockStatus',
            label: t('Stock status'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => <WoocommerceStockStatus
                stockClass={row.stock.availability.stockClass}
                stockText={row.stock.availability.text}
            />,
            visible: showColumns.stock,
        },
        {
            id: 'actions',
            label: t('Actions'),
            desktopProperties: {
                headerAlign: 'center',
                cellAlign: 'center',
            },
            mobileProperties: {},
            renderCell: (row) => {
                return <>
                    {mutable && <GridActionsCellItem onClick={invokeRemoveRowCallback(row)} icon={<DeleteIcon/>} label="Delete"/>}
                    <AddToCartButton
                        wishlistId={wishlistId}
                        row={row}
                        itemId={row.id}
                        text={row.addToCart.text}
                        url={row.addToCart.url}
                        requiresSelection={row.addToCart.requiresSelection}
                        lockInterfaceCallback={lockInterfaceUntilPromiseIsDone}
                    />
                </>
            },
            visible: showColumns.actions,
        }
    ]

    const onDragEnd = (result) => {
        if (!result.destination) {
            return
        }

        if (result.source.index === result.destination.index) {
            return
        }

        const initialRows = [...rows]

        const reorderedRows = reorder(
            initialRows,
            result.source.index,
            result.destination.index
        )

        const reorderedList = reorderedRows.map((row, index) => {
            return {
                id: row.id,
                priority: index
            }
        })

        dispatch(setList(reorderedRows))

        return dispatch(commitNewOrdinationProductOfWishlistAsync({
            wishlistId: wishlistId, list: reorderedList,
        })).then((result) => {
            result.error && dispatch(setList(initialRows))
        })
    }

    const DesktopTable = () => {
        return <TableContainer>
            <Table sx={{ minWidth: 650 }}>
                <TableHead>
                    <TableRow>
                        {columns.filter((column) => column.visible).map((column) => (
                            <TableCell key={column.id} align={column.desktopProperties.headerAlign}>
                                {column.renderColumn ? column.renderColumn() : column.label}
                            </TableCell>
                        ))}
                    </TableRow>
                </TableHead>

                <DragDropContext onDragEnd={onDragEnd}>
                    <Droppable droppableId="droppable_desktop">
                        {(provided) => (
                            <TableBody
                                {...provided.droppableProps}
                                ref={provided.innerRef}
                            >
                                {rows.map((row, index) => (
                                    <Draggable
                                        draggableId={row.id.toString()}
                                        key={row.id}
                                        index={index}
                                        isDragDisabled={!mutable}
                                    >
                                        {(provided) => {
                                            return (
                                                <TableRow
                                                    ref={provided.innerRef}
                                                    {...provided.draggableProps}
                                                    {...provided.dragHandleProps}
                                                >
                                                    {columns.filter((column) => column.visible).map((column) => (
                                                        <TableCell key={column.id}
                                                                   align={column.desktopProperties.cellAlign}>
                                                            {column.renderCell(row)}
                                                        </TableCell>
                                                    ))}
                                                </TableRow>
                                            )
                                        }}
                                    </Draggable>
                                ))}
                                {rows.length === 0 &&
                                    <TableRow>
                                        <TableCell align="center" colSpan={columns.filter((column) => column.visible).length}>
                                            {t('No products added to the wishlist')}
                                        </TableCell>
                                    </TableRow>
                                }
                                {provided.placeholder}
                            </TableBody>
                        )}
                    </Droppable>
                </DragDropContext>
            </Table>
        </TableContainer>
    }

    const MobileList = () => {
        let listItemColumnThumbnail = columns.filter((column) => column.id === 'thumbnail').shift()
        let listItemColumnTitle = columns.filter((column) => column.id === 'title').shift()
        let listItemColumnUnitPrice = columns.filter((column) => column.id === 'unitPrice').shift()
        let listItemColumnBulkSelectCheckbox = columns.filter((column) => column.id === 'bulkSelectCheckbox').shift()
        let listItemColumnDeleteControl = columns.filter((column) => column.id === 'deleteControl').shift()
        const excludes = [
            listItemColumnThumbnail,
            listItemColumnTitle,
            listItemColumnUnitPrice,
            listItemColumnBulkSelectCheckbox,
            listItemColumnDeleteControl
        ].filter((item) => !!item)
        let listItemColumns = columns.filter((column) => !excludes.includes(column))

        return <DragDropContext onDragEnd={onDragEnd}>
            <Droppable droppableId="droppable_mobile">
                {(provided) => (
                    <List
                        {...provided.droppableProps}
                        ref={provided.innerRef}
                        sx={{
                            '& > *': { marginTop: 1, marginBottom: 1 },
                        }}
                    >
                        {rows.map((row, index) => (
                            <Draggable
                                draggableId={row.id.toString()}
                                key={row.id}
                                index={index}
                                isDragDisabled={!mutable}
                            >
                                {(provided) => {
                                    return (
                                        <Card
                                            ref={provided.innerRef}
                                            {...provided.draggableProps}
                                            {...provided.dragHandleProps}
                                        >
                                            <ListItem>
                                                {
                                                    listItemColumnBulkSelectCheckbox && listItemColumnBulkSelectCheckbox.visible &&
                                                    <ListItemIcon>
                                                        {listItemColumnBulkSelectCheckbox.renderCell(row)}
                                                    </ListItemIcon>
                                                }
                                                {
                                                    listItemColumnDeleteControl && listItemColumnDeleteControl.visible &&
                                                    <ListItemIcon>
                                                        {listItemColumnDeleteControl.renderCell(row)}
                                                    </ListItemIcon>
                                                }
                                                <ListItemAvatar>
                                                    {listItemColumnThumbnail.renderCell(row)}
                                                </ListItemAvatar>
                                                <ListItemText
                                                    primary={listItemColumnTitle.renderCell(row)}
                                                    secondary={listItemColumnUnitPrice.renderCell(row)}
                                                />
                                            </ListItem>

                                            <List>
                                                {listItemColumns.filter((column) => column.visible).map((column) => (
                                                    <ListItem key={column.id} dense={true}
                                                              secondaryAction={column.renderCell(row)}>
                                                        <ListItemText>
                                                            {column.label}
                                                        </ListItemText>
                                                    </ListItem>
                                                ))}
                                            </List>
                                        </Card>
                                    )
                                }}
                            </Draggable>
                        ))}
                        {rows.length === 0 &&
                            <ListItem>
                                {t('No products added to the wishlist')}
                            </ListItem>
                        }
                        {provided.placeholder}
                    </List>
                )}
            </Droppable>
        </DragDropContext>
    }

    const theme = useTheme()
    const isDesktop = useMediaQuery(theme.breakpoints.up('md'))

    const onBulkActionApply = (action) => {
        let senderPromise = null;

        if ( action === "add-to-cart" ) {
            senderPromise = dispatch(batchAddToCartItemsOfWishlistAsync({
                wishlistId: wishlistId,
                relationshipIds: checked,
                isDelete: config.getConfig().settings.remove_if_added_to_cart,
                isRedirect: config.getConfig().settings.redirect_to_cart
            }));
        } else if ( action === "delete" ) {
            senderPromise = dispatch(batchDeleteItemsOfWishlistAsync({
                wishlistId: wishlistId,
                relationshipIds: checked
            }));
        } else if ( action === "add-all-to-cart" ) {
            senderPromise = dispatch(addToCartAllItemsOfWishlistAsync({
                wishlistId: wishlistId
            }));
        } else {
            showErrorMessage(t("Incorrect bulk action"));
            return;
        }

        senderPromise.then(getRefreshedFragments);
        senderPromise.then((result) => {
            result.payload.messages.forEach((message) => {
                if ( message.type === "success" ) {
                    showSuccessMessage(`${message.entityTitle}: ${message.message}`)
                } else if ( message.type === "error" ) {
                    showErrorMessage(`${message.entityTitle}: ${message.message}`)
                }
            });
        });
        senderPromise.then(() => {
            setChecked([])
        });

        return lockInterfaceUntilPromiseIsDone(senderPromise)
    }

    const bulkActions = [
        {
            value: "add-to-cart",
            label: t('Add to cart')
        },
        ...(mutable ? [{
            value: "delete",
            label: t('Delete')
        }] : [])
    ]

    return (
        <Box
            sx={{
                width: '100%'
            }}
        >
            <Box>
                <div className={'top-menu'}></div>

                <NotificationsBlock/>

                {config.getConfig().share.shareBlockPosition === 'before_wishlist' && rows.length > 0 &&
                    <ShareBlock></ShareBlock>
                }
                <Box sx={{ opacity: loading ? 0.1 : 1.0 }}>
                    {isDesktop ? <DesktopTable/> : <MobileList/>}
                    <Backdrop sx={{ position: 'absolute', backgroundColor: 'inherit' }} open={loading}/>
                </Box>
                {config.getConfig().share.shareBlockPosition === 'after_wishlist' && rows.length > 0 &&
                    <ShareBlock></ShareBlock>
                }
                {rows.length > 0 &&
                    <BulkActions bulkActions={bulkActions} onBulkActionApply={onBulkActionApply} isDesktop={isDesktop}/>
                }
                {config.getConfig().share.shareBlockPosition === 'after_bulk_actions' && rows.length > 0 &&
                    <ShareBlock></ShareBlock>
                }
            </Box>
        </Box>
    )
}

export default ItemsOfWishlistList
