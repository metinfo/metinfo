<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<section class="met-showdownload animsition">
    <div class="container">
        <div class="row">
            <div class="col-md-12 met-showdownload-body" m-id='noset' >
                <div class="row">
                    <section class="details-title">
                        <h1 class='m-t-10 m-b-5'>{$data.title}</h1>
                        <div class="info">
                            <span>{$data.updatetime}</span>
                            <span>{$data.issue}</span>
                            <span>
                                <i class="icon wb-eye m-r-5" aria-hidden="true"></i>
                                {$data.hits}
                            </span>
                        </div>
                    </section>
                    <section class="download-paralist">
                        <if value="$data['para']">
                            <list data="$data['para']" name="$s">
                            <dl class="dl-horizontal clearfix blocks font-size-16">
                                <dt class='font-weight-300'><span>{$s.name}</span> :<span class="p-x-10">{$s.value}</span></dt>
                            </dl>
                            </list>
                        </if>
                        <a class="btn btn-outline btn-primary btn-squared met-showdownload-btn m-t-20" href="{$data.downloadurl}" title="{$data.title}">{$lang.download}</a>
                    </section>
                    <section class="met-editor clearfix">
                        {$data.content}
                    </section>
                   <if value="$data['taglist']">
                   <div class="tag-border">
                        <div class="detail_tag font-size-14">
                            <span>{$data.tagname}</span>
                            <list data="$data['taglist']" name="$tag">
                                <a href="{$tag.url}" {$g.urlnew} title="{$tag.name}">{$tag.name}</a>
                            </list>
                        </div>
                        <if value="$data['tag_relations']">
                        <div class="met-relevant">
                            <ul class='blocks-100 blocks-md-2'>
                                <list data="$data['tag_relations']" name="$rel">
                                    <li>
                                        <h4 class='m-y-0'>
                                            <a href='{$rel.url}' title='{$rel.title}' {$g.urlnew}>{$rel.title}[{$rel.updatetime}]</a>
                                        </h4>
                                    </li>
                                </list>
                            </ul>
                        </div>
                        </if>
                    </div>
                    </if>
                </div>
            </div>
            
        </div>
    </div>
</section>
<include file="foot.php" />