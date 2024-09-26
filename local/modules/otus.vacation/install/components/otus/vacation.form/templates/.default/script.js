BX.namespace('Otus.Vacation.Request')

BX.Otus.Vacation.Request = {
    vacationRequestDescription: '',
    vacationRequestId: 0,
    permissions: {},
    componentVacationNode: null,
    items: [],
    absenceData: {},
    vacationDays: 0,
    dateRangeConfigs: {
        locale: {
            autoUpdateInput: false,
            format: 'DD.MM.YYYY',
            cancelLabel: 'Отмена',
            applyLabel: "Подтвердить",
            fromLabel: "С",
            toLabel: "По",
            weekLabel: "W",
            daysOfWeek: [
                "Вс",
                "Пн",
                "Вт",
                "Ср",
                "Чт",
                "Пт",
                "Сб"
            ],
            monthNames: [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль",
                "Август",
                "Сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
            firstDay: 1,
        }
    },
    errors: {
        noDate: 'Не указан период отпуска',
    },
    init: function (params) {
        this.items = params.items

        this.componentVacationNode = document.getElementById('vacation_periods_elements')
        this.componentVacationNode.classList.add('vacation__items')

        this.vacationRequestId = "vacationRequestId" in params ? params.vacationRequestId : 0
        this.vacationRequestDescription = params.vacationRequestDescription
        this.permissions = params.permissions
        this.absenceData = params.absenceData
        this.dateRangeConfigs["minDate"] = params.currentDate
        this.dateRangeConfigs["cross_dates"] = this.absenceData

        this.checkVacationItems()
        this.recalculateVacationDays()

        this.renderVacation(true)
    },
    createVacation: function () {
        let vacation = {
            from: null,
            to: null,
            fromFormatted: null,
            toFormatted: null,
            info: {},
            messages: [],
            status: 'NEW',
            id: 0,
            days: 0,
        }
        this.items.push(vacation)
        this.renderVacation()
    },
    drawVacation: function (index, item) {

        let vacationDaysNode = document.getElementById('vacation_days_count')

        vacationDaysNode.innerText = this.vacationDays

        let descriptionNode = document.getElementById('vacation_request_description')

        if (descriptionNode) {

            descriptionNode.value = this.vacationRequestDescription

        }

        let node = document.createElement('div')
        node.id = 'vacation_' + index
        node.classList.add('vacation__item')

        let nodeWrapper = document.createElement('div')

        let dateWrapper = document.createElement('div')
        dateWrapper.classList.add('ui-ctl')

        let dateInput = document.createElement('input')
        dateInput.classList.add('ui-ctl-element')

        let dateIcon = document.createElement('div')
        dateIcon.classList.add('ui-ctl-after', 'ui-ctl-icon-calendar')
        dateWrapper.appendChild(dateInput)
        dateWrapper.appendChild(dateIcon)

        nodeWrapper.appendChild(dateWrapper)

        $(dateInput).daterangepicker(this.dateRangeConfigs)

        if (item.from == null || item.to == null) {
            $(dateInput).val('')
        } else {
            $(dateInput).data('daterangepicker').setStartDate(item.fromFormatted)
            $(dateInput).data('daterangepicker').setEndDate(item.toFormatted)
        }

        if (this.hasPermission('can_edit')) {
            $(dateInput).on('apply.daterangepicker', function (ev, picker) {
                item.from = picker.startDate.format('YYYY-MM-DD')
                item.to = picker.endDate.format('YYYY-MM-DD')
                item.fromFormatted = picker.startDate.format('DD.MM.YYYY')
                item.toFormatted = picker.endDate.format('DD.MM.YYYY')
                BX.Otus.Vacation.Request.checkVacationItems()
                BX.Otus.Vacation.Request.recalculateVacationDays()
                BX.Otus.Vacation.Request.renderVacation()
            })
            $(dateInput).on('showCalendar.daterangepicker', function(dateRangePickerObject) {

            })
        } else {
            $(dateInput).prop("disabled", true)
        }

        if (item.messages.length > 0) {
            let modalIcon = document.createElement('span')
            modalIcon.classList.add('vacation__info-icon')
            modalIcon.addEventListener('click', () => {
                let contentBlock = document.createElement('div')

                for (let i of item.messages) {
                    var message = document.createElement('div')
                    message.classList.add('ui-alert', i.class, 'ui-alert-icon-warning', 'message__block')
                    message.innerText = i.text
                    contentBlock.appendChild(message)
                }

                let popup = BX.PopupWindowManager.create("vacation-message_" + Math.random().toFixed(3), null, {
                    content: contentBlock.innerHTML,
                    width: 500,
                    height: 400,
                    zIndex: 100,
                    closeIcon: {
                        opacity: 1
                    },
                    titleBar: 'Замечания по отпуску',
                    closeByEsc: true,
                    darkMode: false,
                    autoHide: false,
                    draggable: true,
                    resizable: true,
                    min_height: 100,
                    min_width: 100,
                });

                popup.show();
            })
            nodeWrapper.appendChild(modalIcon)
        }

        if (this.hasPermission('can_edit')) {
            let deleteButton = document.createElement('span')
            deleteButton.classList.add('vacation__close-icon')

            deleteButton.addEventListener('click', () => {
                this.deleteVacationByIndex(index)
            })

            nodeWrapper.appendChild(deleteButton)
        }

        let daysCountNode = document.createElement('div')

        daysCountNode.classList.add('days-count')

        daysCountNode.innerHTML = item.days

        nodeWrapper.appendChild(daysCountNode)

        node.appendChild(nodeWrapper)

        let itemActionBlock = document.createElement('div')

        let approveItem = document.createElement('span')

        approveItem.classList.add('green-approve', 'item__underline')

        approveItem.innerText = 'Согласовать'

        approveItem.addEventListener('click', () => {
            let contentBlock = document.createElement('form')

            let textarea = document.createElement('textarea')
            textarea.classList.add('textarea__modal')
            textarea.id = 'ITEM_APPROVE_TEXTAREA_' + index

            let buttonWrapper = document.createElement('div')
            buttonWrapper.classList.add('button-wrapper__modal')

            let approveButton = document.createElement('button')
            approveButton.classList.add('ui-btn', 'ui-btn-success')
            approveButton.innerText = 'Согласовать'
            approveButton.id = 'ITEM_APPROVE_' + index

            contentBlock.appendChild(textarea)
            buttonWrapper.appendChild(approveButton)
            contentBlock.appendChild(buttonWrapper)

            let popup = BX.PopupWindowManager.create("vacation-item-approve_" + index, null, {
                content: contentBlock.innerHTML,
                width: 500,
                height: 400,
                zIndex: 100,
                closeIcon: {
                    opacity: 1
                },
                titleBar: 'Комментарий к утверждению',
                closeByEsc: true,
                darkMode: false,
                autoHide: false,
                draggable: true,
                resizable: true,
                min_height: 100,
                min_width: 100,
            })

            popup.show()

            approveButton = document.getElementById('ITEM_APPROVE_' + index)

            approveButton.addEventListener('click', () => {
                this.approveVacationItem(index)
                this.renderVacation()
            })
        })

        let rejectItem = document.createElement('span')

        rejectItem.classList.add('red-reject', 'item__action', 'item__underline')

        rejectItem.innerText = 'Отклонить'

        rejectItem.addEventListener('click', () => {
            let contentBlock = document.createElement('form')

            let textarea = document.createElement('textarea')
            textarea.classList.add('textarea__modal')
            textarea.id = 'ITEM_REJECT_TEXTAREA_' + index

            let buttonWrapper = document.createElement('div')
            buttonWrapper.classList.add('button-wrapper__modal')

            let rejectButton = document.createElement('button')
            rejectButton.classList.add('ui-btn', 'ui-btn-danger')
            rejectButton.innerText = 'Отклонить'
            rejectButton.id = 'ITEM_REJECT_' + index

            contentBlock.appendChild(textarea)
            buttonWrapper.appendChild(rejectButton)
            contentBlock.appendChild(buttonWrapper)

            let popup = BX.PopupWindowManager.create("vacation-item-reject_" + index, null, {
                content: contentBlock.innerHTML,
                width: 500,
                height: 400,
                zIndex: 100,
                closeIcon: {
                    opacity: 1
                },
                titleBar: 'Причина отклонения',
                closeByEsc: true,
                darkMode: false,
                autoHide: false,
                draggable: true,
                resizable: true,
                min_height: 100,
                min_width: 100,
            })

            popup.show()

            rejectButton = document.getElementById('ITEM_REJECT_' + index)

            rejectButton.addEventListener('click', () => {
                this.rejectVacationItem(index)
                this.renderVacation()
            })
        })

        let alternativeItem = document.createElement('span')

        alternativeItem.classList.add('blue-alternative', 'item__action', 'item__underline')

        alternativeItem.innerText = 'Предложить даты'
        alternativeItem.addEventListener('click', () => {
            let contentBlock = document.createElement('form')

            let dateInputAlternative = document.createElement('input')
            dateInputAlternative.classList.add('ui-ctl-element')
            dateInputAlternative.id = 'ITEM_ALTERNATIVE_INPUT_' + index

            let dateWrapperAlternative = document.createElement('div')
            dateWrapperAlternative.classList.add('ui-ctl')

            let dateIconAlternative = document.createElement('div')
            dateIconAlternative.classList.add('ui-ctl-after', 'ui-ctl-icon-calendar')

            let textareaTitle = document.createElement('div')
            textareaTitle.classList.add('textarea__title')
            textareaTitle.innerText = 'Комментарий к изменениям'

            let textarea = document.createElement('textarea')
            textarea.classList.add('textarea__modal')
            textarea.id = 'ITEM_ALTERNATIVE_TEXTAREA_' + index

            let buttonWrapper = document.createElement('div')
            buttonWrapper.classList.add('button-wrapper__modal')

            let alternativeButton = document.createElement('button')
            alternativeButton.classList.add('ui-btn', 'ui-btn-primary')
            alternativeButton.innerText = 'Предложить изменения'
            alternativeButton.id = 'ITEM_ALTERNATIVE_' + index

            dateWrapperAlternative.appendChild(dateInputAlternative)
            dateWrapperAlternative.appendChild(dateIconAlternative)
            contentBlock.appendChild(dateWrapperAlternative)
            contentBlock.appendChild(textareaTitle)
            contentBlock.appendChild(textarea)
            buttonWrapper.appendChild(alternativeButton)
            contentBlock.appendChild(buttonWrapper)

            let popup = BX.PopupWindowManager.create("vacation-item-alternative_" + index, null, {
                content: contentBlock.innerHTML,
                width: 500,
                height: 500,
                zIndex: 100,
                closeIcon: {
                    opacity: 1
                },
                titleBar: 'Предложить изменения',
                closeByEsc: true,
                darkMode: false,
                autoHide: false,
                draggable: true,
                resizable: true,
                min_height: 100,
                min_width: 100,
            })
            popup.show()

            alternativeButton = document.getElementById('ITEM_ALTERNATIVE_' + index)

            let alternativeDateInput = $('.popup-window-content input').daterangepicker(this.dateRangeConfigs)

            if (item.from == null || item.to == null) {
                $(alternativeDateInput).val('')
            } else {
                $(alternativeDateInput).data('daterangepicker').setStartDate(item.fromFormatted)
                $(alternativeDateInput).data('daterangepicker').setEndDate(item.toFormatted)
            }

            $(alternativeDateInput).on('apply.daterangepicker', function (ev, picker) {
                item.info.dateFrom = picker.startDate.format('YYYY-MM-DD')
                item.info.dateTo = picker.endDate.format('YYYY-MM-DD')
            })

            alternativeButton.addEventListener('click', () => {
                this.alternativeVacationItem(index)
                this.renderVacation()
            })
        })

        if (this.hasPermission('can_approve')) {
            // itemActionBlock.appendChild(approveItem) - убираем кнопку согласования VacationItem 
            itemActionBlock.appendChild(rejectItem)
            itemActionBlock.appendChild(alternativeItem)
            itemActionBlock.style = 'pointer-events: auto;'
        }

        node.appendChild(itemActionBlock)

        this.componentVacationNode.appendChild(node)

        let info = document.createElement('div')

        if (item.info.type != null && item.info.text) {
            switch (item.info.type) {
                case 'success':
                    info.classList.add('ui-alert', 'ui-alert-success', 'ui-alert-icon-warning', 'alert-box__text')
                    info.innerText = item.info.text
                    break
                case 'error':
                    info.classList.add('ui-alert', 'ui-alert-danger', 'ui-alert-icon-danger', 'alert-box__text')
                    info.innerText = item.info.text
                    break
                case 'warning':
                    info.classList.add('ui-alert', 'ui-alert-warning', 'ui-alert-icon-warning', 'alert-box__text')
                    info.innerText = item.info.text
                    break
            }
            this.componentVacationNode.appendChild(info)
        }

    },
    deleteVacationByIndex: function (index) {
        if (index > -1) {
            this.items.splice(index, 1)
        }
        this.recalculateVacationDays()
        this.renderVacation()
    },
    renderVacation: function (firstRender = false) {
        let descriptionNode = document.getElementById('vacation_request_description')

        if (descriptionNode !== null) {

            if (this.vacationRequestDescription != descriptionNode.value && !firstRender) {

                this.vacationRequestDescription = descriptionNode.value

            } else {

                descriptionNode.value = this.vacationRequestDescription

            }

        }

        this.componentVacationNode.innerHTML = ''
        for (let i in this.items) {
            this.drawVacation(i, this.items[i])
        }
    },
    save: function (callback = null) {
        let hasErrors = false

        for (let item of this.items) {
            if (item.from == null || item.to == null) {
                hasErrors = true
                item.info.type = "error"
                item.info.text = this.errors.noDate
            } else {
                item.info = {}
            }
        }

        if (hasErrors) {
            this.renderVacation()
            return false
        }

        let vacationParams = {}

        let description = document.getElementById('vacation_request_description').value
        vacationParams.description = description

        if(document.getElementById('REQUESTED_USER')){
            let requestedUser = document.getElementById('REQUESTED_USER').value

            if (requestedUser.length == 0) {
                this.showError('Не указан сотрудник по заявке')
                return false
            }

            vacationParams.requestedUser = requestedUser
        }

        if(document.getElementById('VACATION_TYPE')){
            let vacationType = document.getElementById('VACATION_TYPE').value

            if (vacationType.length == 0) {
                this.showError('Не указан тип отпуска')
                return false
            }

            vacationParams.vacationType = vacationType
        }

        BX.ajax.runComponentAction('otus:vacation.form', 'saveVacationRequest', {
            mode: 'class',
            data: {
                vacationRequestId: this.vacationRequestId,
                items: this.items,
                vacationParams: vacationParams,
            }
        }).then(response => {
                if (typeof (callback) == "function") {
                    callback(response)
                } else {
                    if (BX.SidePanel.Instance.isOpen()) {
                        try {
                            BX.SidePanel.Instance.close()
                        } catch (err) {
                            console.log(err)
                        }
                    }
                }
            }, reject => {
                let errorMessages = reject.errors.map(e => e.message).join('\n')
                this.showError(errorMessages)
            }
        )

    },
    close: function () {
        if (BX.SidePanel.Instance.isOpen()) {
            try {
                BX.SidePanel.Instance.close()
            } catch (err) {
                console.log(err)
            }
        }
    },
    startVacationRequestApproval: function () {
        let callback = function (response) {
            BX.ajax.runComponentAction("otus:vacation.form", "startVacationRequestApproval", {
                mode: "class",
                data: {
                    vacationRequestId: response.data["vacation_request_id"]
                }
            }).then(response => {
                if (BX.SidePanel.Instance.isOpen()) {
                    BX.SidePanel.Instance.close()
                }
            }, reject => {
                let errorMessages = reject.errors.map(e => e.message).join('\n')
                this.showError(errorMessages)
            })
        }

        this.save(callback)
    },
    approveVacationRequest: function () {

        for (item of this.items) {
            if (item.info.type == 'warning' || item.info.type == 'error') {
                this.showError('Вы не согласовали все отпуска')
                return
            }
        }

        let callback = function (vacationRequestId = 0) {
            let approveComment = document.getElementById('approver_comment').value

            if (approveComment === '') {
                this.showError('Не заполнено поле комментарий')
                return
            }

            BX.ajax.runComponentAction('otus:vacation.form', 'approveVacationRequest', {
                mode: 'class',
                data: {
                    vacationRequestId: vacationRequestId,
                    approveComment: approveComment,
                },
            }).then(response => {
                let popupWindow = BX.PopupWindowManager.getCurrentPopup()
                popupWindow.close()
                if (BX.SidePanel.Instance.isOpen()) {
                    BX.SidePanel.Instance.close()
                }
            }, reject => {
                let errorMessages = reject.errors.map(e => e.message).join('\n')
                this.showError(errorMessages)
            })
        }
        this.showApproverPopup('vacation-request-approve', 'approve', callback)
    },
    rejectVacationRequest: function () {
        let callback = function (vacationRequestId = 0, items = {}) {
            let rejectComment = document.getElementById('approver_comment').value

            if (rejectComment === '') {
                this.showError('Не заполнено поле комментарий')
                return
            }

            BX.ajax.runComponentAction('otus:vacation.form', 'rejectVacationRequest', {
                mode: 'class',
                data: {
                    vacationRequestId: vacationRequestId,
                    items: items,
                    rejectComment: rejectComment,
                },
            }).then(response => {
                let popupWindow = BX.PopupWindowManager.getCurrentPopup()
                popupWindow.close()
                if (BX.SidePanel.Instance.isOpen()) {
                    BX.SidePanel.Instance.close()
                }
            }, reject => {
                let errorMessages = reject.errors.map(e => e.message).join('\n')
                this.showError(errorMessages)
            })
        }
        this.showApproverPopup('vacation-request-reject', 'reject', callback)
    },
    alternativeVacationRequest: function () {
        let callback = function (vacationRequestId = 0, items = {}) {
            let rejectComment = document.getElementById('approver_comment').value

            if (rejectComment === '') {
                this.showError('Не заполнено поле комментарий')
                return
            }

            BX.ajax.runComponentAction('otus:vacation.form', 'rejectVacationRequest', {
                mode: 'class',
                data: {
                    vacationRequestId: vacationRequestId,
                    items: items,
                    rejectComment: rejectComment,
                    alternative: true,
                },
            }).then(response => {
                let popupWindow = BX.PopupWindowManager.getCurrentPopup()
                popupWindow.close()
                if (BX.SidePanel.Instance.isOpen()) {
                    BX.SidePanel.Instance.close()
                }
            }, reject => {
                let errorMessages = reject.errors.map(e => e.message).join('\n')
                this.showError(errorMessages)
            })
        }
        this.showApproverPopup('vacation-request-alternative', 'alternative', callback)
    },
    hasPermission: function (permissionType) {
        if (permissionType in this.permissions) {
            return this.permissions[permissionType]
        } else {
            return false
        }
    },
    showApproverPopup: function (popupWindowId, action = '', callback = null) {
        let contentBlock = document.createElement('form')

        let textarea = document.createElement('textarea')
        textarea.classList.add('textarea__modal')
        textarea.id = 'approver_comment'

        let buttonWrapper = document.createElement('div')
        buttonWrapper.classList.add('button-wrapper__modal')

        let approveButton = document.createElement('button')
        approveButton.innerText = 'Продолжить'
        approveButton.id = 'action_button_' + action

        switch (action) {
            case 'approve':
                approveButton.classList.add('ui-btn', 'ui-btn-success')
                break
            case 'reject':
                approveButton.classList.add('ui-btn', 'ui-btn-danger')
                break
            case 'alternative':
                approveButton.classList.add('ui-btn', 'ui-btn-primary')
        }

        contentBlock.appendChild(textarea)
        buttonWrapper.appendChild(approveButton)
        contentBlock.appendChild(buttonWrapper)

        let popup = BX.PopupWindowManager.create(popupWindowId, null, {
            content: contentBlock.innerHTML,
            width: 500,
            height: 400,
            zIndex: 100,
            closeIcon: {
                opacity: 1
            },
            titleBar: 'Комментарий',
            closeByEsc: true,
            darkMode: false,
            autoHide: false,
            draggable: true,
            resizable: true,
            min_height: 100,
            min_width: 100,
        })

        popup.show()

        if (typeof (callback) == "function") {
            let actionButton = document.getElementById(approveButton.id)
            actionButton.addEventListener('click', () => {
                callback(this.vacationRequestId, this.items)
            })
        }
    },
    rejectVacationItem: function (index) {
        let popup = BX.PopupWindowManager.getCurrentPopup()
        let rejectComment = document.getElementById('ITEM_REJECT_TEXTAREA_' + index).value

        if (rejectComment === '') {
            this.showError('Не заполнено поле комментарий')
            return
        }

        this.items[index].info.type = 'error'
        this.items[index].info.text = 'Отклонено' + '\n Комментарий: ' + rejectComment

        popup.close()
    },
    alternativeVacationItem: function (index) {
        let popup = BX.PopupWindowManager.getCurrentPopup()
        let alternativeComment = document.getElementById('ITEM_ALTERNATIVE_TEXTAREA_' + index).value
        let alternativeDate = document.getElementById('ITEM_ALTERNATIVE_INPUT_' + index).value

        if (alternativeComment === '') {
            this.showError('Не заполнен комментарий')
            return
        }

        this.items[index].info.type = 'warning'
        this.items[index].info.text = 'Предложены новые даты: ' + alternativeDate + '\n Комментарий: ' + alternativeComment

        popup.close()
    },
    approveVacationItem: function (index) {
        let popup = BX.PopupWindowManager.getCurrentPopup()
        let approveComment = document.getElementById('ITEM_APPROVE_TEXTAREA_' + index).value

        this.items[index].info.type = 'success'
        this.items[index].info.text = 'Отпуск в выбранные даты утвержден.'

        if (approveComment !== '') {
            this.items[index].info.text += '\n Комментарий: ' + approveComment
        }

        popup.close()
    },
    checkVacationItems: function () {
        let isFromBefore = false
        let isToAfter = false
        let isFromSameOrAfter = false
        let isFromSameOrBefore = false
        let isToSameOrBefore = false
        let isToSameOrAfter = false

        for (item of this.items) {
            item.messages = []

            for (vacation of this.absenceData.vacations) {

                isFromBefore = moment(item.from).isSameOrBefore(vacation.DATE_FROM)
                isToAfter = moment(item.to).isSameOrAfter(vacation.DATE_TO)
                isFromSameOrAfter = moment(item.from).isSameOrAfter(vacation.DATE_FROM)
                isFromSameOrBefore = moment(item.from).isSameOrBefore(vacation.DATE_TO)
                isToSameOrAfter = moment(item.to).isSameOrAfter(vacation.DATE_FROM)
                isToSameOrBefore = moment(item.to).isSameOrBefore(vacation.DATE_TO)

                if (isFromSameOrAfter && isFromSameOrBefore || isToSameOrAfter && isToSameOrBefore || isFromBefore && isToAfter) {
                    item.messages.push({text: 'Отпуск пересекается с утвержденным отпуском сотрудника ' + vacation.USER_NAME + '\n' + vacation.FORMATTED_DATE_FROM + ' - ' + vacation.FORMATTED_DATE_TO, class: 'ui-alert-danger'})
                }
            }
            for (request of this.absenceData.requests) {

                isFromBefore = moment(item.from).isSameOrBefore(request.DATE_FROM)
                isToAfter = moment(item.to).isSameOrAfter(request.DATE_TO)
                isFromSameOrAfter = moment(item.from).isSameOrAfter(request.DATE_FROM)
                isFromSameOrBefore = moment(item.from).isSameOrBefore(request.DATE_TO)
                isToSameOrAfter = moment(item.to).isSameOrAfter(request.DATE_FROM)
                isToSameOrBefore = moment(item.to).isSameOrBefore(request.DATE_TO)

                if (isFromSameOrAfter && isFromSameOrBefore || isToSameOrAfter && isToSameOrBefore || isFromBefore && isToAfter) {
                    item.messages.push({text: 'Отпуск пересекается с планируемым отпуском сотрудника ' + request.USER_NAME + '\n' + request.FORMATTED_DATE_FROM + ' - ' + request.FORMATTED_DATE_TO, class: 'ui-alert-warning'})
                }
            }
        }
    },
    showError: function (errorMessage) {
        alert(errorMessage)
    },
    recalculateVacationDays: function () {
        let items = this.items

        this.vacationDays = 0

        for (item of items) {
            var from = moment(item.from)
            var to = moment(item.to)

            var daysBetween = to.diff(from, 'days')

            item.days = (daysBetween + 1)

            this.vacationDays += daysBetween
        }
    }
}
