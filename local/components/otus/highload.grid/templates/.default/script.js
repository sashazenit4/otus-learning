BX.namespace('Otus.HighloadGrid')

BX.Otus.HighloadGrid = {
    gridId: null,
    init: function (initData) {
        initData = JSON.parse(initData)
        this.gridId = initData.gridId
    },
    deleteItem: function (id) {
        BX.ajax.runComponentAction('otus:highload.grid', 'delete', {
            mode: "class",
            data: {
                id: id,
            }
        }).then(response => {
            grid = BX.Main.gridManager.getInstanceById(this.gridId)
            if (grid) {
                grid.reloadTable()
            }
        }, reject => {
            let errorMessages = reject.errors.map(e => e.message).join('\n')
            this.showError(errorMessages)
        })
    },
    showError: function (message) {
        alert(message)
    }
}
