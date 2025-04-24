import Plugin from 'src/plugin-system/plugin.class';

export default class PudoPlugin extends Plugin {
    init() {
        const zipCode = this.el.getAttribute('zipcode');
        const selectedMethodId = document.querySelectorAll('input[name="shippingMethodId"]:checked')[0].value;

        const shippingMethodNames = {};
        const shippingMethods = this.el.getAttribute('shippingMethods');
        const shippingMethodsJson = JSON.parse(shippingMethods);

        for (let shippingMethodId in shippingMethodsJson) {
            shippingMethodNames[shippingMethodId] = shippingMethodsJson[shippingMethodId].technicalName;
        }

        if(shippingMethodNames[selectedMethodId] === 'shipping_pudo') {
            console.log('pudo', 'current selected', document.querySelector('select[name="pudo-point"]').value)
            this.fetchPudoData(zipCode);
            document.querySelector('.pudo-point-wrapper').classList.remove('d-none');
            const selectedPudoPoint = document.querySelector('select[name="pudo-point"]').value;
            if(!document.querySelector('select[name="pudo-point"]').value ||
                document.querySelector('select[name="pudo-point"]').value == "-1") {
                document.querySelector("#confirmFormSubmit").setAttribute("disabled", "disabled");
            } else {
                document.querySelector("#confirmFormSubmit").removeAttribute("disabled");
            }
        } else {
            document.querySelector('.pudo-point-wrapper').classList.add('d-none');
        }

        document.querySelector('#pudo-point').addEventListener('change', (event) => {
            if (event.target.value && event.target.value != "-1") {
                document.querySelector("#confirmFormSubmit").removeAttribute("disabled");
            } else {
                document.querySelector("#confirmFormSubmit").setAttribute("disabled", "disabled");
            }

            event.target.closest('form').submit();
        });
    }

    fetchPudoData(zipCode) {
        const url = `/pudo-points/${zipCode}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(async response => {
            let pudoPoints = [];
            const responseJson = await response.json();

            if (responseJson?.dealers.length) {
                pudoPoints = responseJson.dealers.map(dealer => {
                    return {
                        id: dealer.dealerID,
                        name: dealer.dealerName,
                        address: dealer.dealerAddress1 || dealer.dealerAddress2,
                        city: dealer.dealerCity,
                        state: dealer.dealerProvince,
                        zip: dealer.dealerPostal,
                        phone: dealer.dealerPhone,
                        distance: dealer.dealerDistance
                    }
                });

                document.querySelector('#pudo-error').classList.add('d-none');
            } else {
                document.querySelector('#pudo-error').classList.remove('d-none');
            }

            const Select = document.querySelector('select[name="pudo-point"]');

            pudoPoints.forEach(point => {
                const option = document.createElement('option');
                option.value = point.id;
                option.innerHTML = `${point.name} - ${point.city}, ${point.state} - ${point.address}, (${point.distance} miles)`;
                if (responseJson?.selectedPudoPoint && responseJson?.selectedPudoPoint === point.id) {
                    option.setAttribute('selected', 'selected');
                }
                Select.appendChild(option);
            });

            if (responseJson?.selectedPudoPoint && responseJson?.selectedPudoPoint !== "-1") {
                document.querySelector("#confirmFormSubmit").removeAttribute("disabled");
            }

            Select.addEventListener("change", function(event) {
                if (event.target.value && event.target.value != "-1") {
                    document.querySelector("#confirmFormSubmit").removeAttribute("disabled");
                } else {
                    document.querySelector("#confirmFormSubmit").setAttribute("disabled", "disabled");
                }
            });
        })
    }
}
