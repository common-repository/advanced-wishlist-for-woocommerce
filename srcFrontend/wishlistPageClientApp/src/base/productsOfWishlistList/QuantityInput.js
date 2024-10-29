import React from 'react'
import { TextField } from '@mui/material'
import { commitNewQuantityProductOfWishlistAsync, setQuantityByRowId } from 'Store/slices/productsOfWishlistSlice'
import { useDispatch } from 'react-redux'

const QuantityInput = (props) => {
    const { wishlistId, row, lockInterfaceCallback } = props

    const dispatch = useDispatch()
    const timeoutRef = React.useRef()
    const [value, setValue] = React.useState(row.quantity)

    return <TextField
        type={'number'}
        value={value}
        onChange={(e) => {
            const intValue = parseInt(e.target.value, 10)

            if (!isNaN(intValue) && intValue > 0) {
                setValue(intValue)
                clearTimeout(timeoutRef.current)
                timeoutRef.current = setTimeout(() => {
                    lockInterfaceCallback(dispatch(commitNewQuantityProductOfWishlistAsync({
                        wishlistId: wishlistId,
                        relationshipId: row.id,
                        quantity: intValue
                    }))
                        .then((result) => {
                            !result.error && dispatch(setQuantityByRowId({ rowId: row.id, quantity: intValue }))
                        }))
                }, 1000)
            }
        }}
        variant="standard"
    />
}

export default QuantityInput
