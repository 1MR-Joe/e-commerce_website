/**
 * @param {string} api
 * @param {string} format
 * @returns {Promise<string|null>}
 */
export async function fetchData(api, format) {
    let response = await fetch(api);

    if(! response.ok) {
        console.error('error fetching data');
        return null;
    } else {
        let data;

        if(format === 'json') {
            data = await response.json();
        } else if(format === 'text') {
            data = await response.text();
        }

        return data;
    }
}

export function fetchCategories(api = '/api/categories', format = 'json') {
    let categories;

    fetchData(api, format).then(data =>  {
        console.log(data);
        categories =  data;
    });

    console.log(categories);
    return categories;
}

/**
 * @param {string} api
 * @param {HTMLElement} container
 * @returns {Promise<ChildNode>}
 */
export async function fetchHtml(api, container = null) {
    let response = await fetch(api);

    let html = await response.text();

    if(container) {
        container.innerHTML = html;
        return container.firstChild;
    }

    let tmpDiv = document.createElement('div');
    tmpDiv.innerHTML = html;
    return tmpDiv.firstChild;
}
