<form method="POST" action="{$url.own_name}c=imgmanager&a=doSaveWaterMark" class='watermark-form'
      data-submit-ajax='1' id="watermark-form" data-validate_order="#watermark-form">
    <div class="metadmin-fmbx">
        <dl>
            <dt>
                <label class='form-control-label'>{$word.setimgWatermarkType}</label>
            </dt>
            <dd>
                <div class='form-group clearfix'>
                    <div class="custom-control custom-radio ">
                        <input type="radio" id="met_wate_class-1" name="met_wate_class" value='1' class="custom-control-input" />
                        <label class="custom-control-label" for="met_wate_class-1">{$word.setimgWordWatermark}</label>
                    </div>
                    <div class="custom-control custom-radio ">
                        <input type="radio" id="met_wate_class-2" name="met_wate_class" value='2' class="custom-control-input" />
                        <label class="custom-control-label" for="met_wate_class-2">{$word.setimgImgWatermark}</label>
                    </div>
                </div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.setimgWatermark}</label>
            </dt>
            <dd>
                <div class='form-group clearfix'>
                    <div class="custom-control custom-checkbox checkbox-inline">
                        <input type="checkbox" id="met_big_wate" name='met_big_wate' value='1' class="custom-control-input" />
                        <label class="custom-control-label" for="met_big_wate">{$word.setimgBigImg}</label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-inline">
                        <input type="checkbox" id="met_thumb_wate" name='met_thumb_wate' value='1' class="custom-control-input" />
                        <label class="custom-control-label" for="met_thumb_wate">{$word.setimgThumb}</label>
                    </div>
                </div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.setimgPosition}</label>
            </dt>
            <dd>
                <div class='form-group clearfix'>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-1" name="met_watermark" value='1' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-1">{$word.setimgLeftTop}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-5" name="met_watermark" value='5' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-5">{$word.setimgTopMid}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-2" name="met_watermark" value='2' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-2">{$word.setimgRightTop}</label>
                    </div>
                </div>
                <div class='form-group clearfix'>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-8" name="met_watermark" value='8' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-8">{$word.setimgLeftMid}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-0" name="met_watermark" value='0' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-0">{$word.setimgMid}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-6" name="met_watermark" value='6' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-6">{$word.setimgRightMid}</label>
                    </div>
                </div>
                <div class='form-group clearfix'>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-4" name="met_watermark" value='4' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-4">{$word.setimgLeftLow}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-7" name="met_watermark" value='7' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-7">{$word.setimgLowMid}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="met_watermark-3" name="met_watermark" value='3' class="custom-control-input " />
                        <label class="custom-control-label" for="met_watermark-3">{$word.setimgRightLow}</label>
                    </div>
                </div>
            </dd>
        </dl>

        <dl>
            <dt>
                <label class='form-control-label'>保持图片动画效果</label>
            </dt>
            <dd>
                <div class='form-group clearfix'>
                    <input type="checkbox" data-plugin="switchery" name="met_wate_img_gif_hold" value="0"  />
                    <span class="text-help ml-2">开启该设置后将保持GIF图片动画效果，不再生成GIF类型图片的水印图和缩略图</span>
                </div>
            </dd>
        </dl>

        <!--水印比例-->
        <dl>
            <dt>
                <label class='form-control-label'>水印比例设置（0%~100%）</label>
            </dt>
            <dd>
                <div class='form-group clearfix'>
                    <div class="d-inline-block">
                        <input type="text"  class="text-center form-control" data-min="0" data-max="100" data-plugin="touchSpin" name="met_wate_img_scale" autocomplete="off" style="display: block;">
                        <span class="text-help ml-2 scale_tips_1 hide">设置水印在原图中所占比例,启用设置后水印文字大小设置将失效以该设置为准</span>
                        <span class="text-help ml-2 scale_tips_2 hide">设置水印在原图中所占比例,不设置则按照水印实际始尺寸处理</span>
                    </div>

                </div>
            </dd>
        </dl>


        <div class="met_wate_class-1 hide">
            <!--水印文字-->
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgWord}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <input type="text" name="met_text_wate" class="form-control">
                        <span class="text-help ml-2">{$word.setimgTip3}</span>
                    </div>
                </dd>
            </dl>

            <!--水印字体大小-->
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgWordSize2}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <input type="text" class="text-center" data-min="0" data-max="100" data-plugin="touchSpin"
                               name="met_text_bigsize" autocomplete="off">
                        <span class="text-help ml-2">{$word.setflashPixel}</span>
                    </div>
                </dd>
            </dl>
            <!--缩略图水印字体大小-->
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgWordSize}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <input type="text" class="text-center" data-min="0" data-max="100" data-plugin="touchSpin"
                               name="met_text_size" autocomplete="off">
                        <span class="text-help ml-2">{$word.setflashPixel}</span>
                    </div>
                </dd>
            </dl>
            <!--水印文字角度-->
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgWordAngle}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <input type="text" class="text-center" data-min="0" data-max="360" data-plugin="touchSpin"
                               name="met_text_angle" autocomplete="off">
                        <span class="text-help ml-2">{$word.setimgTip5}</span>
                    </div>
                </dd>
            </dl>
            <!--水印文字颜色-->
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgWordColor}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <input type="text" name="met_text_color" class="form-control" data-plugin='minicolors'>
                    </div>
                </dd>
            </dl>

            <!--水印字体-->
            <?php
            $upload = array(
                'title' => $word['setimgWordFont'],
                'name' => 'met_text_fonts',
                'noimage' => 1,
                'type' => 'file',
                'format' => 'ttf',
                'tips' => $word['setimgTip4']
            );
            ?>
            <include file="pub/content_details/upload"/>
        </div>
        <div class="met_wate_class-2 hide">
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgImg}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <div class="d-inline-block">
                            <input type="file" name="met_wate_img" data-plugin='fileinput' accept="image/jpeg,image/png">
                        </div>
                        <span class="text-help ml-2">{$word.setimgTip2}</span>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt>
                    <label class='form-control-label'>{$word.setimgImg2}</label>
                </dt>
                <dd>
                    <div class='form-group clearfix'>
                        <div class="d-inline-block">
                            <input type="file" name="met_wate_bigimg" data-plugin='fileinput' accept="image/jpeg,image/png">
                        </div>
                        <span class="text-help ml-2">{$word.setimgTip2}</span>
                    </div>
                </dd>
            </dl>
        </div>
        <dl>
            <dt></dt>
            <dd>
                <button type="submit" class='btn btn-primary' id="btn-save">{$word.Submit}</button>
            </dd>
        </dl>
    </div>
</form>