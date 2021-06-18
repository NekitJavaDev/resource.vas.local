// Для начала нужно получить на странице полный список зданий с устройствами в них
// Пройтись по ним массивом и зарендерить все согласно шаблону
import app_constants from "../constants";
const { base_url } = app_constants;

const getJson = url => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
        })
            .done(resolve)
            .fail(reject)
    });
}

const iconNames = {
    'electricity': 'flash',
    'water': 'tint',
    'heat': 'fire'
};

function getElementFromTemplate(templateString, containerElement = { tag: 'div', className: '' }) {
    const container = document.createElement(containerElement.tag);
    container.className = containerElement.className;

    container.innerHTML = templateString;

    return container;
}

class Meter {
    constructor(meter) {
        this.id = meter.id
        this.name = meter.name;
        this.type = meter.typeName;
        this.active = meter.active;
        this.buildingId = meter.building_id;
        this.converterId = meter.converter_id;
        this.description = meter.description;
        this.driverId = meter.driver_id;
        this.model = meter.model;
        this.modelFullName = meter.model_full_name;
        this.name = meter.name;
        this.rs_port = meter.rs_port;
        this.serialNumber = meter.serial_number;
        this.serverIp = meter.server_ip;
        this.typeId = meter.type_id;
        this.verificationDate = meter.verification_date;
        this.worked = meter.worked;
        this.isActive = this.active === 1;
        this.isWorked =  this.worked === 1;
        this.isNotActive = !this.isActive;
        this.isErrorConnect = this.serverIp === null;
    }

    get template() {
        let className;
        if (this.isActive) {
            className='active';
        }
        if (this.isWorked) {
            className = 'worked';
        }
        if (this.isNotActive) {
            className = 'is_not_active';
        }
        if (this.isErrorConnect) {
            className = 'is_error_connect';
        }
        return `
            <li
                tab-index="-1"
                class="obs-devices__item obs-devices__item--${className}"
                id=meter_id_${this.id}
                onclick="document.location.href = '${base_url + 'meters/' + this.id}'"
            >
                <span class="obs-devices__item-icon">
                    <i class="fa fa-${iconNames[this.type]}"></i>
                </span>
            </li>
        `.trim();
    }
};

class Building {
    constructor(building) {
        this.id = building.id
        this.name = building.short_name;
        this.meters = building.meters_arr.map(meter => new Meter(meter));
    }

    get template() {
        return `
            <li class="obs-building__item" id=building_id_${this.id}>
                <header class="obs-building__item-header">
                    <a class="obs-building__item-title" href=${base_url + 'buildings/' + this.id} style="text-align: center">
                        ${this.name}
                    </a>
                    <svg width="31" height="31" class="page-header__icon page-header__icon--search">
                        <use xlink:href="img/icons/sprite.svg#building-icon"></use>
                    </svg>
                </header>
                <div class="obs-building__item-body">
                    <ul class="obs-devices__list">
                        ${this.meters.map(meter => meter.template).join(``)}
                    </ul>
                </div>
            </li>`.trim();
    }
};

class BuildingList {
    constructor() {
        this.buildingListUrl = `${base_url}/building_list`;
        // this.meterValuesUrl = `${base_url}/meters/values`;
    }

    loadSpinner() {
        return null;
    }

    renderBuildings() {
        const template = this.buildingList.map(building => building.template)
            .join(``).trim();

        const container = {
            tag: 'ul',
            className: 'obs-building__list'
        };

        this.domList = getElementFromTemplate(template, container);

        const wrapper = document.querySelector('.buildings-list');
        wrapper.appendChild(this.domList);
    }

    async showElemenet() {
        const buildingsData = await getJson(this.buildingListUrl);

        this.buildingList = buildingsData
                .map(buildingData => new Building(buildingData))
                .filter(building => building.meters.length > 0);

        this.renderBuildings();
    }

    async showElementsByFilter(meterValues) {
        const buildingsData = meterValues;

        this.buildingList = buildingsData
                .map(buildingData => new Building(buildingData))
                .filter(building => building.meters.length > 0);

        this.renderBuildings();
        this.clearMeterValuesInBuildingList();
    }
    
    async clearMeterValuesInBuildingList() {
        const wrapper = document.querySelector('.obs-building__list')
        wrapper.remove();
    }

    get elements() {
        return this.buildingList;
    }
};

function getData(url, type, requestData, onSuccess, onError) {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: type,
        url: url,
        data: requestData,

        success: onSuccess,
        error: onError,
    });
}

const app = new BuildingList();
app.showElemenet();

$(function () {
    $('#btn_save_filter').click(function (e) {
        var url = $(this).attr("data-url");

        var meterStatus = document.getElementById('meterStatusSelect').value;
        var meterTypeId = document.getElementById('meterTypeSelect').value;
        var sectorId = document.getElementById('sectorSelect').value;

        var requestData = {};
        if (meterStatus !== 'null') {
            requestData.meterStatus = meterStatus
        }
        if (meterTypeId !== 'null') {
            requestData.meterTypeId = meterTypeId
        }
        if (sectorId !== 'null') {
            requestData.sectorId = sectorId
        }

        getData(
            url,
            "post",
            requestData,
            (data) => {app.showElementsByFilter(data)},
            (error) => {console.log(error)},
        );

    });

    $('#btn_reset_filter').click(function(e) {
        var url = $(this).attr("data-url");

        getData(
            url,
            "post",
            {},
            (data) => { app.showElementsByFilter(data) },
            (error) => { console.log(error) },
        );
    })
});