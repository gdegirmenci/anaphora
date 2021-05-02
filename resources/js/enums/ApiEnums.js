/**
 * @class ApiEnums
 */
class ApiEnums {
    constructor() {
        this.PREFIX = '/api';
        this.FETCH_CAMPAIGNS_URL = `${this.PREFIX}/campaigns/get`;
        this.FETCH_DASHBOARD_URL = `${this.PREFIX}/dashboard/get`;
        this.CREATE_CAMPAIGN_URL = `${this.PREFIX}/campaigns/create`;
    }
}

export default new ApiEnums();
