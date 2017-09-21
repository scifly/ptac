<div class="form-group">
    <label for="mobile" class="col-sm-3 control-label">手机号码</label>
    <div class="col-sm-6">
        <table id="mobileTable" class="table-bordered table-responsive" style="width: 100%;">
            <thead>
            <tr>
                <td>手机号码</td>
                <td style="text-align: center;">默认</td>
                <td style="text-align: center;">启用</td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            @if(isset($custodian->user->mobiles))
                @foreach($custodian->user->mobiles as $key => $mobile)
                    <tr>
                        <td>
                            <input class="form-control"
                                   name="mobile[{{ $key }}][mobile]"
                                   placeholder="（请输入手机号码）"
                                   value='{{ $mobile->mobile }}'
                                   required
                                   pattern="/^1[0-9]{10}$/">
                            <input class="form-control"
                                   name="mobile[{{ $key }}][id]"
                                   type="hidden"
                                   value='{{ $mobile->id }}'>
                        </td>
                        <td style="text-align: center;">
                            <label for="mobile[isdefault]"></label>
                            <input name="mobile[isdefault]"
                                   value="{{ $key }}"
                                   id="mobile[isdefault]"
                                   type="radio"
                                   class="minimal"
                                   required
                                   @if($mobile->isdefault) checked @endif
                            />
                        </td>
                        <td style="text-align: center;">
                            <label for="mobile[{{ $key }}][enabled]"></label>
                            <input name="mobile[{{ $key }}][enabled]"
                                   value="{{ $mobile->enabled }}"
                                   id="mobile[{{ $key }}][enabled]"
                                   type="checkbox"
                                   class="minimal"
                                   @if($mobile->enabled) checked @endif
                            />
                        </td>
                        <td style="text-align: center;">
                            @if($key == sizeof($custodian->user->mobiles) - 1)
                                <span class="input-group-btn">
                                                <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                                    <i class="fa fa-plus text-blue"></i>
                                                </button>
                                            </span>
                            @else
                                <span class="input-group-btn">
                                                <button class="btn btn-box-tool btn-remove btn-mobile-remove" type="button">
                                                    <i class="fa fa-minus text-blue"></i>
                                                </button>
                                            </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <input class="form-control"
                       type="hidden"
                       id="mobile-size"
                       value={{sizeof($custodian->user->mobiles)}}>
            @else
                <tr>
                    <td>
                        <input class="form-control"
                               name="mobile[0][mobile]"
                               placeholder="（请输入手机号码）"
                               value=''
                               required
                               pattern="/^1[0-9]{10}$/">
                    </td>
                    <td style="text-align: center;">
                        <input name="mobile[isdefault]" value="0" checked type="radio" class="minimal">
                    </td>
                    <td style="text-align: center;">
                        <input name="mobile[0][enabled]"  checked type="checkbox" class="minimal">
                    </td>
                    <td style="text-align: center;">
                        <span class="input-group-btn">
                            <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                <i class="fa fa-plus text-blue"></i>
                            </button>
                        </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>