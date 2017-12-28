@foreach($departments as $department)
    <div class="air-choose-item" style="position: relative;">
        <label class="weui-cell weui-check__label" id="group-{{ $department->id }}" data-item="{{ $department->id }}" data-uid="{{ $department->id }}" data-type="group">
            <div class="weui-cell__hd">
                <input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
                <i class="weui-icon-checked"></i>
            </div>
            <div class="weui-cell__bd">
                <img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" style="border-radius: 0;" class="js-go-detail lazy" width="75" height="75">
                <span class="contacts-text">{{ $department->name }}</span>
            </div>
        </label>
        <a class="icon iconfont icon-jiantouyou show-group" style="position:absolute;top: 0;right:0;height: 55px;line-height:55px;z-index: 1;width: 30px;"></a>
    </div>
@endforeach

@foreach($users as $user)
    <div class="air-choose-item" style="position: relative;">
        <label class="weui-cell weui-check__label" id="person-{{ $user->id }}" data-item="{{ $user->id }}" data-uid="{{ $user->id }}" data-type="person">
            <div class="weui-cell__hd">
                <input type="checkbox" class="weui-check choose-item-btn" name="checkbox">
                <i class="weui-icon-checked"></i>
            </div>
            <div class="weui-cell__bd">
                <img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
                <span class="contacts-text">{{ $user->realname }}</span>
            </div>
        </label>
    </div>
@endforeach