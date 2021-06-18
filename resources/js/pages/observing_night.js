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
    }

    get template() {
        const t = `
            <li
                tab-index="-1"
                class="obs-devices__item"
                id="meter_id_${this.id}"
                onclick="document.location.href = '${base_url + 'meters/night_water/' + this.id}'"
            >
                <span class="obs-devices__item-icon">
                    <i style="color: #84DBFF;" class="fa fa-${iconNames[this.type]}"></i>
                </span>
            </li>
        `.trim();
        console.log(t);
        return t;
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
        this.buildingListUrl = `${base_url}building_list_night_water`;
        // this.meterValuesUrl = `${base_url}water_meters/values`;
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

        const wrapper = document.querySelector('.content-wrapper');
        wrapper.appendChild(this.domList);
    }

    async showElemenet() {
        const buildingsData = await getJson(this.buildingListUrl);

        this.buildingList = buildingsData
                .map(buildingData => new Building(buildingData))
                .filter(building => building.meters.length > 0);

        this.renderBuildings();
    }

    async clearMeterValuesInBuildingList() {
        const wrapper = document.querySelector('.obs-building__list')
        wrapper.remove();
    }

    get elements() {
        return this.buildingList;
    }
};

const app = new BuildingList();
app.showElemenet();