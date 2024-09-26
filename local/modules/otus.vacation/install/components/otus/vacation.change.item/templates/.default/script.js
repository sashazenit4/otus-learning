BX.namespace('Otus.Vacation.Request')

BX.Otus.Vacation.Change = {
    vacationRequestDescription: '',
    vacationRequestId: 0,
    permissions: {},
    componentVacationNode: null,
    items: [],
    vacationDays: 0,
    absenceData: {},
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
        this.items.push({
            from: null,
            to: null,
            fromFormatted: null,
            toFormatted: null,
            info: {},
            messages: [],
            status: 'NEW',
            id: 0,
            days: 0,
        })

        this.componentVacationNode = document.getElementById('vacation_periods_elements')
        this.componentVacationNode.classList.add('vacation__items')

        this.vacationRequestId = "vacationRequestId" in params ? params.vacationRequestId : 0

        this.absenceData = params.absenceData
        this.dateRangeConfigs["minDate"] = params.currentDate
        this.dateRangeConfigs["cross_dates"] = this.absenceData

        this.checkVacationItems()

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

        $(dateInput).on('apply.daterangepicker', function (ev, picker) {
            item.from = picker.startDate.format('YYYY-MM-DD')
            item.to = picker.endDate.format('YYYY-MM-DD')
            item.fromFormatted = picker.startDate.format('DD.MM.YYYY')
            item.toFormatted = picker.endDate.format('DD.MM.YYYY')
            BX.Otus.Vacation.Change.checkVacationItems()
            BX.Otus.Vacation.Change.recalculateVacationDays()
            BX.Otus.Vacation.Change.renderVacation()
        })
        $(dateInput).on('showCalendar.daterangepicker', function(dateRangePickerObject) {

        })

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

        let deleteButton = document.createElement('span')

        deleteButton.classList.add('vacation__close-icon')
        deleteButton.addEventListener('click', () => {
            this.deleteVacationByIndex(index)
        })

        let daysCountNode = document.createElement('div')

        daysCountNode.classList.add('days-count')

        daysCountNode.innerHTML = item.days

        nodeWrapper.appendChild(daysCountNode)

        nodeWrapper.appendChild(deleteButton) 

        node.appendChild(nodeWrapper)

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

        if(document.getElementById('vacation_initiator')){
            let requestedUser = document.querySelector('#vacation_initiator table').getAttribute('bx-tooltip-user-id')

            if (requestedUser.length == 0) {
                this.showError('Не указан сотрудник по заявке')
                return false
            }

            vacationParams.requestedUser = requestedUser
        }

        if(document.querySelector('#vacation_item')){
            let replacedOptions = document.querySelectorAll('#vacation_item option')

            let currentReplacedItem = ''

            let lastReplacedItem = ''

            for (let repItem of replacedOptions) {
                if (repItem.selected) {
                    currentReplacedItem += repItem.value + ' '
                    lastReplacedItem = repItem.value
                }
            }

            vacationTypeOptionList = document.querySelectorAll('#vacation_item_types option')

            let currentVacationType = ''

            for (let vacationType of vacationTypeOptionList) {
                if (lastReplacedItem == vacationType.value) {
                    currentVacationType = vacationType.innerText
                }
            }

            vacationParams.vacationType = currentVacationType
            vacationParams.replacedItem = currentReplacedItem
            vacationParams.isChangeRequest = true;
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
    hasPermission: function (permissionType) {
        if (permissionType in this.permissions) {
            return this.permissions[permissionType]
        } else {
            return false
        }
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
