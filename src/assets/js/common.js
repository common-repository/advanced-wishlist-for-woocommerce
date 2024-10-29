jQuery( document ).ready( function ( $ ) {
    const addToWishlistBtns = document.getElementsByClassName('algol-add-to-wishlist-btn');
    let showNotice = false;
    let popup = null, popupMsg = null;
    let timerShowPopup = null, timerFadePopup = null;
    let savedVariationAttrs = null, savedVariationId = null;
    if (document.getElementById('algol-wishlist-popup-message')) {
        popup = document.getElementById('algol-wishlist-popup-message');
        popupMsg = document.getElementById('algol-wishlist-message');
        showNotice = true;
    }
    for (const btn of addToWishlistBtns) {
        btn.addEventListener('click', async (event) => {
            const btn = event.currentTarget;

            const productId = savedVariationId !== null ? savedVariationId : btn.dataset.productId;
            const variation = savedVariationAttrs !== null ? savedVariationAttrs : {};

            $(btn).addClass("loading");

            const result = window.awlApi.wishlistAndProductsApi.addItemIntoDefaultWishlist({
                productId: parseInt(productId),
                variation: variation
            })
              .catch(err => {
                $(btn).removeClass("loading");
                console.log(err)
            });

            result.then((r) => {
                console.log(r);
                const parent = btn.parentElement.parentElement;
                const altBtn = parent.querySelector('.algol-remove-from-wishlist-btn, .algol-view-wishlist-btn');
                if (altBtn) {
                  btn.style.display = 'none';
                  altBtn.style.display = null;
                  if (altBtn.classList.contains('algol-remove-from-wishlist-btn')) {
                    altBtn.dataset.relId = r.data.id;
                  }
                  if (altBtn.classList.contains('algol-view-wishlist-btn')) {
                    altBtn.dataset.wishlistUrl = r.data.wishlistUrl;
                  }
                }
                let wishlistProductIds = btn.dataset.wishlistProductIds ? JSON.parse(btn.dataset.wishlistProductIds) : [];
                const wishlistAddData = {
                    productId: r.data.productId,
                    variation: r.data.variation,
                    relationshipId: r.data.id,
                };
                wishlistProductIds.push(wishlistAddData);
                btn.dataset.wishlistProductIds = JSON.stringify(wishlistProductIds);

                $(btn).removeClass("loading");
                if (showNotice) {
                    popupMsg.innerHTML = algolWishlistAppData.labels.productAddedToWishlist;
                    if (timerShowPopup) {
                        clearTimeout(timerShowPopup);
                    }
                    if (timerFadePopup) {
                        clearInterval(timerFadePopup);
                    }
                    popup.style.display = 'block';
                    popup.style.opacity = 1;
                    timerShowPopup = setTimeout(fade, 1000, popup);
                }
            });
        });
    }

    const removeFromWishlistBtns = document.getElementsByClassName('algol-remove-from-wishlist-btn');
    for (const btn of removeFromWishlistBtns) {
        btn.addEventListener('click', (event) => {
            const btn = event.currentTarget;
            let relId = 0;
            const parent = btn.parentElement.parentElement;
            const altBtn = parent.querySelector('.algol-add-to-wishlist-btn');
            let wishlistProductIds = JSON.parse(altBtn.dataset.wishlistProductIds);
            if (savedVariationId !== null && savedVariationAttrs !== null) {
                for (const el of wishlistProductIds) {
                    if (el.productId === savedVariationId &&
                        JSON.stringify(el.variation) === JSON.stringify(savedVariationAttrs))
                    {
                        relId = el.relationshipId;
                        break;
                    }
                }
            } else {
                relId = wishlistProductIds[0].relationshipId;
            }
            $(btn).addClass("loading");

            const result = window.awlApi.wishlistAndProductsApi.deleteItemOfDefaultWishlistById(parseInt(relId))
                .catch(err => {
                    $(btn).removeClass("loading");
                    console.log(err)
                });

            result.then((r) => {
                console.log(r);
                if (altBtn) {
                    const filteredWishlistProductIds = wishlistProductIds.filter((el) => {
                        return el.relationshipId !== relId;
                    });
                    altBtn.dataset.wishlistProductIds = JSON.stringify(filteredWishlistProductIds);
                    btn.style.display = 'none';
                    altBtn.style.display = null;
                }
                $(btn).removeClass("loading");
                if (showNotice) {
                    popupMsg.innerHTML = algolWishlistAppData.labels.productRemovedFromWishlist;
                    if (timerShowPopup) {
                        clearTimeout(timerShowPopup);
                    }
                    if (timerFadePopup) {
                        clearInterval(timerFadePopup);
                    }
                    popup.style.display = 'block';
                    popup.style.opacity = 1;
                    timerShowPopup = setTimeout(fade, 1000, popup);
                }
            });
        });
    }

    const viewWishlistBtns = document.getElementsByClassName('algol-view-wishlist-btn');
    for (const btn of viewWishlistBtns) {
      btn.addEventListener('click', async (event) => {
        const btn = event.currentTarget;
        const wishlistUrl = btn.dataset.wishlistUrl;
        window.open(wishlistUrl, '_blank');
      });
    }

    jQuery('.variations_form').on('found_variation', {variationForm: this},
        function (event, variation) {
            savedVariationId = variation.variation_id;
            savedVariationAttrs = variation.attributes;
            for (const attrKey in savedVariationAttrs) {
                if (savedVariationAttrs[attrKey] === '') {
                    const attrControl = jQuery(event.target).find(`[name='${attrKey}']`);
                    savedVariationAttrs[attrKey] = attrControl.val();
                }
            }
            const wishlistProductIds = JSON.parse(jQuery('.algol-add-to-wishlist-btn').attr('data-wishlist-product-ids'));
            let variationFound = false;
            for (const el of wishlistProductIds) {
                if (variation.variation_id === el.productId &&
                    JSON.stringify(variation.attributes) === JSON.stringify(el.variation)) {
                    jQuery('.algol-remove-from-wishlist-btn').show();
                    jQuery('.algol-view-wishlist-btn').show();
                    jQuery('.algol-add-to-wishlist-btn').hide();
                    variationFound = true;
                }
            }
            if (!variationFound) {
                jQuery('.algol-remove-from-wishlist-btn').hide();
                jQuery('.algol-view-wishlist-btn').hide();
                jQuery('.algol-add-to-wishlist-btn').show();
            }
            jQuery('.algol-add-to-wishlist-btn').prop('disabled', false);
    });

    jQuery('.variations_form').on('hide_variation', {variationForm: this},
        function () {
            jQuery('.algol-add-to-wishlist-btn').prop('disabled', true);
        });

    function fade(element) {
        let op = 1;  // initial opacity
        timerFadePopup = setInterval(function () {
            if (op <= 0.02) {
                clearInterval(timerFadePopup);
                element.style.display = 'none';
                element.style.opacity = 1;
            }
            element.style.opacity = op;
            element.style.filter = 'alpha(opacity=' + op * 100 + ")";
            op -= op * 0.02;
        }, 10);
    }
});
