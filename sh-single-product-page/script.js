document.addEventListener("DOMContentLoaded", () => {
    const mainImage = document.getElementById("sh-main-image");
    const galleryContainer = document.getElementById("sh-gallery-thumbnails");
    const variationRows = document.querySelectorAll(".sh-variation-row");
    const sizeRowsContainer = document.querySelector(".sh-size-rows-container");
    const addAnotherSizeBtn = document.querySelector(".sh-add-another-size");
    const addToQuoteBtn = document.querySelector(".sh-add-to-quote-btn");

    let currentAttributes = {};
    const productData = window.shProductData || null;

    if (!mainImage || !galleryContainer) return;

    const attachThumbnailListeners = () => {
        const thumbnails = galleryContainer.querySelectorAll(".sh-thumbnail");
        thumbnails.forEach(thumb => {
            thumb.addEventListener("click", () => {
                thumbnails.forEach(t => t.classList.remove("sh-active"));
                thumb.classList.add("sh-active");
                const newImgSrc = thumb.getAttribute("data-img");
                if (newImgSrc) mainImage.src = newImgSrc;
            });
        });
    };

    attachThumbnailListeners();

    if (productData && productData.is_variable) {

        // Helper function to match attributes safely (case-insensitive and prefixed correctly)
        const isMatch = (varAttrs, userAttrs, exactMatch = false) => {
            // Check if all user attributes are satisfied in varAttrs
            for (let key in userAttrs) {
                let uVal = userAttrs[key] ? String(userAttrs[key]).toLowerCase() : "";
                let vVal = varAttrs[key] !== undefined ? String(varAttrs[key]).toLowerCase() : "";

                // If varAttr specifies a value and it doesn't match the user's selected value
                if (vVal !== "" && vVal !== uVal) return false;
            }

            if (exactMatch) {
                // Check if varAttrs requires an attribute the user didn't specify
                for (let key in varAttrs) {
                    let vVal = varAttrs[key] !== undefined ? String(varAttrs[key]).toLowerCase() : "";
                    let uVal = userAttrs[key] ? String(userAttrs[key]).toLowerCase() : "";

                    if (vVal !== "" && uVal === "") return false;
                }
            }

            return true;
        };

        const findMatchingVariation = (attrsObj, exactMatch = false) => {
            if (!productData.variations) return null;

            // Format attrsObj to have 'attribute_' prefix and lowercase keys
            // For image display purposes, we ignore size constraints
            let formattedAttrs = {};
            for (let k in attrsObj) {
                if (k.toLowerCase().indexOf('size') === -1) {
                    formattedAttrs[`attribute_${k.toLowerCase()}`] = attrsObj[k];
                }
            }

            // 1. Try a proper match (either partial or exact)
            let match = productData.variations.find(variation => isMatch(variation.attributes, formattedAttrs, exactMatch));

            // 2. If it's just for image updating (not exact), we fallback to ANY variation that matches at least one selection
            if (!match && !exactMatch && Object.keys(formattedAttrs).length > 0) {
                match = productData.variations.find(variation => {
                    return Object.keys(formattedAttrs).some(key => {
                        let uVal = formattedAttrs[key] ? String(formattedAttrs[key]).toLowerCase() : "";
                        let vVal = variation.attributes[key] !== undefined ? String(variation.attributes[key]).toLowerCase() : "";
                        return vVal !== "" && vVal === uVal;
                    });
                });
            }

            return match;
        };

        const updateProductDisplay = (variation) => {
            if (variation && variation.main_image && variation.main_image.full) {
                mainImage.src = variation.main_image.full;
                let newGalleryHTML = '';
                if (variation.main_image.thumb) {
                    newGalleryHTML += `
                        <div class="sh-thumbnail sh-active" data-img="${variation.main_image.full}">
                            <img src="${variation.main_image.thumb}" alt="Main Thumbnail">
                        </div>`;
                }
                if (variation.gallery && variation.gallery.length > 0) {
                    variation.gallery.forEach(img => {
                        newGalleryHTML += `
                            <div class="sh-thumbnail" data-img="${img.full}">
                                <img src="${img.thumb}" alt="Gallery Thumbnail">
                            </div>`;
                    });
                }
                if (newGalleryHTML) {
                    galleryContainer.innerHTML = newGalleryHTML;
                    attachThumbnailListeners();
                }
            }
        };

        const handleDropdownChange = (e) => {
            const attributeName = e.target.getAttribute("data-attribute");
            const value = e.target.value;
            if (value) currentAttributes[attributeName] = value;
            else delete currentAttributes[attributeName];

            // Only update images if the changed attribute is NOT size
            const isSize = attributeName.toLowerCase().indexOf('size') !== -1;
            if (!isSize) {
                const matchedVariation = findMatchingVariation(currentAttributes, false);
                if (matchedVariation) updateProductDisplay(matchedVariation);
            }
        };

        // Initialize state and listeners for global variation rows (like color)
        variationRows.forEach(row => {
            const attributeName = row.getAttribute("data-attribute");
            const swatches = row.querySelectorAll(".sh-swatch");
            const dropdown = row.querySelector(".sh-variation-selector");
            const selectedNameDisplay = row.querySelector(`.sh-selected-name-${attributeName}`);

            if (swatches.length > 0) {
                swatches.forEach(swatch => {
                    swatch.addEventListener("click", () => {
                        swatches.forEach(s => s.classList.remove("sh-active"));
                        swatch.classList.add("sh-active");
                        const value = swatch.getAttribute("data-value");
                        const name = swatch.getAttribute("data-name");
                        if (selectedNameDisplay) selectedNameDisplay.textContent = name;
                        currentAttributes[attributeName] = value;

                        // Swatches are typically for color/image, so we update
                        const isSize = attributeName.toLowerCase().indexOf('size') !== -1;
                        if (!isSize) {
                            const matchedVariation = findMatchingVariation(currentAttributes, false);
                            if (matchedVariation) updateProductDisplay(matchedVariation);
                        }
                    });
                });
            }

            if (dropdown && dropdown.tagName.toLowerCase() === 'select') {
                if (dropdown.value) currentAttributes[attributeName] = dropdown.value;
                dropdown.addEventListener("change", handleDropdownChange);
            }
        });

        // Initialize state and listeners for size rows
        if (sizeRowsContainer) {
            const initialSizeDropdown = sizeRowsContainer.querySelector('.sh-row-size');
            if (initialSizeDropdown) {
                if (initialSizeDropdown.value) {
                    const attr = initialSizeDropdown.getAttribute('data-attribute');
                    currentAttributes[attr] = initialSizeDropdown.value;
                }
                initialSizeDropdown.addEventListener('change', handleDropdownChange);
            }
        }

        // Handle Add Another Size row logic
        if (addAnotherSizeBtn && sizeRowsContainer) {
            addAnotherSizeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const rows = sizeRowsContainer.querySelectorAll('.sh-size-qty-row');
                if (rows.length === 0) return;

                const firstRow = rows[0];
                const newRow = firstRow.cloneNode(true);

                // clear select value and reset qty
                const select = newRow.querySelector('.sh-row-size');
                if (select) select.value = '';
                const qty = newRow.querySelector('.sh-row-qty');
                if (qty) qty.value = '1';

                // Setup remove button
                const removeBtn = newRow.querySelector('.sh-remove-row');
                if (removeBtn) {
                    removeBtn.style.visibility = 'visible';
                    removeBtn.addEventListener('click', () => {
                        newRow.remove();
                        if (select) select.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                }

                if (select) select.addEventListener('change', handleDropdownChange);
                sizeRowsContainer.appendChild(newRow);
            });
        }
    }

    // Handle Add to Cart via AJAX
    if (addToQuoteBtn && productData) {
        addToQuoteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const originalText = addToQuoteBtn.innerHTML;
            addToQuoteBtn.innerHTML = 'ADDING...';
            addToQuoteBtn.style.opacity = '0.7';
            addToQuoteBtn.style.pointerEvents = 'none';

            let itemsToAdd = [];

            if (productData.is_variable) {
                let globalAttributes = {}; // stores raw attributes e.g. 'pa_color': 'blue'
                // swatches
                const activeSwatches = document.querySelectorAll('.sh-global-attributes .sh-swatch.sh-active');
                activeSwatches.forEach(swatch => {
                    const attr = swatch.closest('.sh-variation-row').getAttribute('data-attribute');
                    const val = swatch.getAttribute('data-value');
                    globalAttributes[attr] = val;
                });

                // other global dropdowns
                const globalDropdowns = document.querySelectorAll('.sh-global-attributes .sh-size-dropdown');
                globalDropdowns.forEach(dropdown => {
                    if (dropdown.value) {
                        const attr = dropdown.getAttribute('data-attribute');
                        globalAttributes[attr] = dropdown.value;
                    }
                });

                const sizeRows = document.querySelectorAll('.sh-size-qty-row');
                if (sizeRows.length > 0) {
                    sizeRows.forEach(row => {
                        const select = row.querySelector('.sh-row-size');
                        const qtyInput = row.querySelector('.sh-row-qty');
                        if (select && select.value) {
                            let itemAttrs = { ...globalAttributes };
                            const sizeAttr = select.getAttribute('data-attribute');
                            itemAttrs[sizeAttr] = select.value;

                            // Find matching variation exactly to add to cart
                            let formattedItemAttrs = {};
                            for (let k in itemAttrs) {
                                formattedItemAttrs[`attribute_${k.toLowerCase()}`] = itemAttrs[k];
                            }
                            const matchedVar = productData.variations.find(v => {
                                // check if all keys in formattedItemAttrs map exactly to this variation
                                for (let key in formattedItemAttrs) {
                                    let uVal = formattedItemAttrs[key] ? String(formattedItemAttrs[key]).toLowerCase() : "";
                                    let vVal = v.attributes[key] !== undefined ? String(v.attributes[key]).toLowerCase() : "";
                                    if (vVal !== "" && vVal !== uVal) return false;
                                }
                                for (let key in v.attributes) {
                                    let vVal = v.attributes[key] !== undefined ? String(v.attributes[key]).toLowerCase() : "";
                                    let uVal = formattedItemAttrs[key] ? String(formattedItemAttrs[key]).toLowerCase() : "";
                                    if (vVal !== "" && uVal === "") return false;
                                }
                                return true;
                            });

                            if (matchedVar) {
                                itemsToAdd.push({
                                    qty: qtyInput ? parseInt(qtyInput.value) : 1,
                                    variation_id: matchedVar.variation_id,
                                    attributes: formattedItemAttrs
                                });
                            }
                        }
                    });
                } else {
                    let formattedGlobalAttrs = {};
                    for (let k in globalAttributes) {
                        formattedGlobalAttrs[`attribute_${k.toLowerCase()}`] = globalAttributes[k];
                    }
                    const matchedVar = productData.variations.find(v => {
                        for (let key in formattedGlobalAttrs) {
                            let uVal = formattedGlobalAttrs[key] ? String(formattedGlobalAttrs[key]).toLowerCase() : "";
                            let vVal = v.attributes[key] !== undefined ? String(v.attributes[key]).toLowerCase() : "";
                            if (vVal !== "" && vVal !== uVal) return false;
                        }
                        for (let key in v.attributes) {
                            let vVal = v.attributes[key] !== undefined ? String(v.attributes[key]).toLowerCase() : "";
                            let uVal = formattedGlobalAttrs[key] ? String(formattedGlobalAttrs[key]).toLowerCase() : "";
                            if (vVal !== "" && uVal === "") return false;
                        }
                        return true;
                    });
                    if (matchedVar) {
                        const qtyInput = document.querySelector('.sh-qty-input');
                        itemsToAdd.push({
                            qty: qtyInput ? parseInt(qtyInput.value) : 1,
                            variation_id: matchedVar.variation_id,
                            attributes: formattedGlobalAttrs
                        });
                    }
                }
            } else {
                const qtyInput = document.querySelector('.sh-qty-input');
                itemsToAdd.push({
                    qty: qtyInput ? parseInt(qtyInput.value) : 1,
                    variation_id: 0,
                    attributes: {}
                });
            }

            if (itemsToAdd.length === 0) {
                alert('Please select all required options before adding to quote.');
                addToQuoteBtn.innerHTML = originalText;
                addToQuoteBtn.style.opacity = '1';
                addToQuoteBtn.style.pointerEvents = 'auto';
                return;
            }

            // AJAX request below
            const formData = new FormData();
            formData.append('action', 'sh_add_to_cart');
            formData.append('product_id', productData.product_id);
            formData.append('nonce', productData.nonce);

            itemsToAdd.forEach((item, index) => {
                formData.append(`items[${index}][qty]`, item.qty);
                if (item.variation_id) {
                    formData.append(`items[${index}][variation_id]`, item.variation_id);
                    Object.keys(item.attributes).forEach(attrKey => {
                        formData.append(`items[${index}][attributes][${attrKey}]`, item.attributes[attrKey]);
                    });
                }
            });

            fetch(productData.ajax_url, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        window.location.href = (res.data && res.data.cart_url) || productData.quote_url || '/request-a-quote/';
                    } else {
                        alert('Error: ' + (res.data ? res.data.message : 'Unknown error'));
                        addToQuoteBtn.innerHTML = originalText;
                        addToQuoteBtn.style.opacity = '1';
                        addToQuoteBtn.style.pointerEvents = 'auto';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred. Please try again.');
                    addToQuoteBtn.innerHTML = originalText;
                    addToQuoteBtn.style.opacity = '1';
                    addToQuoteBtn.style.pointerEvents = 'auto';
                });
        });
    }
});
