<style>.parsley-errors-list.filled {text-align: left}</style>
<div class="form-group">
    <label for="mobile" class="col-sm-3 control-label">手机</label>
    <div class="col-sm-6">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
            <table id="mobiles" class="table-bordered table-responsive"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    <td class="text-center">号码</td>
                    <td class="text-center">默认</td>
                    <td class="text-center">启用</td>
                    <td class="text-center">+/-</td>
                </tr>
                </thead>
                <tbody>
                @if(!empty($mobiles))
                    @foreach($mobiles as $key => $mobile)
                        <tr>
                            <td class="text-center">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-mobile'])
                                    <input class="form-control"
                                           name="mobile[{!! $key !!}][mobile]"
                                           placeholder="(请输入手机号码)"
                                           value='{!! $mobile->mobile !!}'
                                           required
                                           pattern="/^1[0-9]{10}$/"
                                           style="width: 100%"
                                    />
                                    <input class="form-control"
                                           name="mobile[{!! $key !!}][id]"
                                           type="hidden"
                                           value='{!! $mobile->id !!}'
                                    />
                                </div>
                            </td>
                            <td class="text-center">
                                <input name="mobile[isdefault]"
                                       value="{!! $key !!}"
                                       id="mobile[isdefault]{!! $key !!}"
                                       title="默认手机号码"
                                       type="radio"
                                       class="minimal"
                                       required
                                       @if($mobile->isdefault) checked @endif
                                />
                            </td>
                            <td class="text-center">
                                <label for="mobile[{!! $key !!}][enabled]"></label>
                                <input name="mobile[{!! $key !!}][enabled]"
                                       value="{!! $mobile->enabled !!}"
                                       id="mobile[{!! $key !!}][enabled]"
                                       type="checkbox"
                                       class="minimal"
                                       @if($mobile->enabled) checked @endif
                                />
                            </td>
                            <td class="text-center">
                                @if($key == sizeof($mobiles) - 1)
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                            <i class="fa fa-plus text-blue" title="新增"></i>
                                        </button>
                                    </span>
                                @else
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-remove btn-mobile-remove" type="button">
                                            <i class="fa fa-minus text-blue" title="删除"></i>
                                        </button>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <!-- 手机号码数量 -->
                    <input class="form-control" type="hidden" id="count" value={{ sizeof($mobiles) }}>
                @else
                    <tr>
                        <td class="text-center">
                            <div class="input-group">
                                @include('partials.icon_addon', ['class' => 'fa-mobile'])
                                <input class="form-control"
                                       name="mobile[0][mobile]"
                                       placeholder="(请输入手机号码)"
                                       value=''
                                       required
                                       pattern="/^1[0-9]{10}$/"
                                       style="width: 100%"
                                />
                            </div>
                        </td>
                        <td class="text-center">
                            <input id="mobile[isdefault]"
                                   name="mobile[isdefault]"
                                   value="0"
                                   title="默认手机号码"
                                   checked
                                   type="radio"
                                   class="minimal"
                            />
                        </td>
                        <td class="text-center">
                            <label for="mobile[0][enabled]"></label>
                            <input id="mobile[0][enabled]"
                                   name="mobile[0][enabled]"
                                   checked
                                   type="checkbox"
                                   class="minimal"
                            />
                        </td>
                        <td class="text-center">
                            <span class="input-group-btn">
                                <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                    <i class="fa fa-plus text-blue" title="新增"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

    </div>
</div>