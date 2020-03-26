function popupConfirm(orderID) {
    Swal.fire({
        title: 'Claim order?',
        text: 'Are you sure you want to claim this order? You MUST complete it once claimed.',
        showCancelButton: true,
        confirmButtonColor: '#6FEC98',
        cancelButtonColor: '#C8C8C8',
        confirmButtonText: 'Claim',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.replace(`http://home.arturonet.com:8080/apanel/index.php?orderid=${orderID}`)
        }
    });
}

function popupRadio(orderID) {
    const inputOptions = new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                "0": 'In queue',
                "1": 'In progress',
                "2": 'Complete'
            })
        }, 1000)
    })

    Swal.fire({
        title: 'Edit Status',
        input: 'radio',
        inputOptions: inputOptions,
        showCancelButton: true,
        confirmButtonColor: '#6FEC98',
        cancelButtonColor: '#C8C8C8',
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to choose a status!'
            }
        }
    }).then((result) => {
        if (result.value) {
            window.location.replace(`http://home.arturonet.com:8080/apanel/orders.php?orderid=${orderID}&status=${result.value}`)
        }
    });
}

function popupInfo(accountName, accountPassword) {
    Swal.fire({
        title: 'Account Information',
        html:
            `Steam Username: ${accountName}</br> Steam Password: ${accountPassword}`,
        showCancelButton: false,
        confirmButtonColor: '#C8C8C8',
        confirmButtonText: 'Close'
    })
}