BX.namespace('Aclips.Plugin.UIGridCollapse')

/**
 * Плагин для добавления в Main.ui.grid раскрываюзихся списков на статическом контенте
 * @type {{initCollapse: BX.Aclips.Plugin.UIGridCollapse.initCollapse}}
 */
BX.Aclips.Plugin.UIGridCollapse = {

    /**
     * Инициализация плаГина
     * {String} идентификатор грида select
     * {{}} params
     *
     *      ex. params = {
     *          default-collapse: true|false - при загрузке разделы раскрыты или свёрнуты
     *          is-section-selector: String - селектор признак раздела
     *          parent-attribute: String - аттрибут идентификатор родительского элемента (раздела)
     *      }
     */
    initCollapse: function (gridId, params) {

        let sectionSelector = params['is-section-selector'] ?? '[is-section="true"]'
        let parentAttribute = params['parent-attribute'] ?? 'parent'
        let isCollapsed = params['default-collapse'] == true

        let grid = BX.Main.gridManager.getById(gridId)

        if (grid) {
            let container = grid.instance.getContainer()

            /**
             * Раскрытие раздела
             * {HTMLElement} tr
             */
            let open = (tr) => {
                tr.classList.add('main-grid-row-expand')

                let id = tr.getAttribute('data-id')
                let childNodeList = container.querySelectorAll('[' + parentAttribute + '="' + id + '"]')

                Array.from(childNodeList, e => {
                    e.style.display = 'table-row'
                })
            }

            /**
             * Свёртывание раздела
             * {HTMLElement} tr
             */
            let close = (tr) => {
                tr.classList.remove('main-grid-row-expand')

                let id = tr.getAttribute('data-id')
                let childNodeList = container.querySelectorAll('[' + parentAttribute + '="' + id + '"]')

                Array.from(childNodeList, e => {
                    if (e.hasAttribute('is-head')) {
                        close(e)
                    }

                    e.style.display = 'none'
                })
            }

            let sectionNodeList = container.querySelectorAll(sectionSelector)

            Array.from(sectionNodeList, e => {
                let selector = document.createElement('span')
                selector.classList.add('main-grid-plus-button')

                selector.addEventListener('click', e => {
                    let parent = e.target.parentNode.parentNode.parentNode

                    if (parent.classList.contains('main-grid-row-expand')) {
                        close(parent)
                    } else {
                        open(parent)
                    }
                })

                let actionNode = e.querySelector('.main-grid-cell-action .main-grid-cell-content')
                actionNode.innerHTML = ''
                actionNode.appendChild(selector)

                if (isCollapsed) {
                    open(e)
                } else {
                    close(e)
                }
            });

            /**
             * При изменении грида провести повторную инициализацию
             */
            BX.addCustomEvent('Grid::updated', (e) => {
                if (gridId == e.getId()) {
                    this.initCollapse(gridId, params)
                }
            })
        }
    }
}
