<div class="card">
    <div class="card-body p-4">
        <h4 class="card-title">Web Page</h4>
        <x-form-input name="title" label="Page Title" value="{{ old('title', $webPage->title ?? '') }}"
            placeholder="Enter Page Title" required="true" />
        <input type="hidden" name="slug" value="{{ old('slug', $webPage->slug ?? '') }}">
        <h4 class="card-title mt-4">Page Content</h4>
        <textarea name="page_content" id="page_content" class="form-control editor" rows="5"
            placeholder="Enter Page Content">{{ old('page_content', $webPage->page_content ?? '') }}</textarea>
    </div>
</div>


<div class="card">
    <div class="card-body p-4">
        <h4 class="card-title">SEO</h4>
        <div class="row">
            <div class="col-sm-10 mb-2">
                <x-form-input name="heading" label="Heading" value="{{ old('heading', $webPage->heading ?? '') }}"
                    placeholder="Heading" />
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="mb-2">
                    <x-form-input name="meta_title" label="Meta Title"
                        value="{{ old('meta_title', $webPage->meta_title ?? '') }}" placeholder="Meta Title" />
                </div>
                <div class="mb-2">
                    <x-form-input name="meta_keywords" label="Meta Keywords"
                        value="{{ old('meta_keywords', $webPage->meta_keywords ?? '') }}" placeholder="Meta Keywords" />
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="meta_description">Meta Description</label>
                    <textarea name="meta_description" class="form-control" id="meta_description" rows="5"
                        placeholder="Meta Description">{{ old('meta_description', $webPage->meta_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>


<button type="submit" class="btn btn-primary w-md">Submit</button>
</form>


<x-include-plugins :plugins="['chosen', 'datePicker', 'contentEditor']"></x-include-plugins>
