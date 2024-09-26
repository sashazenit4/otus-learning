BX.SidePanel.Instance.bindAnchors({
    rules: [
        {
            condition: [
                "/vacation_request/(\\d+)/",
            ],
            options: {
                requestMethod: "post",
                requestParams: {},
                cacheable: false,
                events: {},
                width: 800,
            },
        },
    ]
});
