BX.namespace('Otus.Vacation.List')

BX.Otus.Vacation.List = {
    showCreateRequestForm: function (vacationRequestId = 0) {
        BX.SidePanel.Instance.open("/vacation_request/" + vacationRequestId + '/', {
            animationDuration: 100,
            width: 800,
            cacheable: false,
            label: {
                text: "Отпуска",
                color: "#FFFFFF", //цвет текста
                bgColor: "#2FC6F6", //цвет фона
                opacity: 80 //прозрачность фона
            },
            events: {
                onClose: function() {
                    let grid = BX.Main.gridManager.getInstanceById('VACATION_GRID')
                    grid.reloadTable()
                }
            }
        })

    },
    showChangeItemForm: function (v) {
        BX.SidePanel.Instance.open("/vacation_request/vacation_change_item/0/", {
            animationDuration: 100,
            width: 800,
            cacheable: false,
            label: {
                text: "Отпуска",
                color: "#FFFFFF", //цвет текста
                bgColor: "#2FC6F6", //цвет фона
                opacity: 80 //прозрачность фона
            },
            events: {
                onClose: function() {
                    let grid = BX.Main.gridManager.getInstanceById('VACATION_GRID')
                    grid.reloadTable()
                }
            }
        })

    },
    deleteRequestById: function (vacationRequestId = 0) {
        BX.ajax.runComponentAction("otus:vacation.form", "deleteVacationRequestById", {
            mode: "class",
            data: {
                vacationRequestId: vacationRequestId,
            }
        }).then(response => {
            let grid = BX.Main.gridManager.getInstanceById('VACATION_GRID')
            grid.reloadTable()
        }, reject => {
            let errorMessages = reject.errors.map(e => e.message).join('\n')
            this.showError(errorMessages)
        })
    }
}
