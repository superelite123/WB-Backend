const { combineReducers } = Redux;
const SetState = (state=initialState,actions) => {
    switch (actions.type) {
        case 'SETPRODUCTATTR':
            return {...state,productAttr:actions.value}
            break;
        case 'CHANGETAXALLOW':
            return {...state,taxAllow:actions.value}
            break;
        case 'ADDITEM':
            state.orderItems = state.orderItems.push(actins.item)
            return state;
            break;
        case 'REMOVEITEM': state.orderItems.filter((element,index) => index != actions.index)
        case 'CHANGEQTY': state.inputData.qty = parseFloat(actions.value)
        case 'CHANGEUNITPRICE': state.inputData.unitPrice = parseFloat(actions.value)
        case 'CHANGEORDERDISCOUNTID':
            state.element = state.element - 1
            return state;
            break;
        default: return state
    }
}

export const combinedReduers = SetState
