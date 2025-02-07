BX.addCustomEvent('onTimeManWindowOpen', function () {
    let a = document.createElement('div')
    let popup = BX.PopupWindowManager.create("timeman-notify", a, {
        content: "Hello World!",
        autoHide: true,
        closeIcon: {
            right: "20px", top: "10px"
        },
        draggable: true,
        closeByEsc: true,
        overlay: {
            backgroundColor: 'red', opacity: '80'
        },
        lightShadow: true,
        darkMode: false,
        events: {
            onPopupShow: function() {
                console.log('Hello world!')
            },
            onPopupClose: function() {
                console.log('Bye world!')
            },
        },
        buttons: [
            new BX.PopupWindowButton({
                text: "Показать секрет",
                className: "popup-window-button-accept",
                events: {
                    click: function(){
                        alert('секрет')
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: "Скрыть попап",
                className: "webform-button-link-cancel",
                events: {
                    click: function(){
                        this.popupWindow.close();
                    }
                }
            }),
        ]
    }
    );
    popup.show();

})