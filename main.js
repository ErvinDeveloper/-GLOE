const filterSelectChange = () => {

    let url = CATEGORY_URL;
    let params = [];

    const level = document.querySelector('.filter-level');
    const theme = document.querySelector('.filter-theme');


    if (level.value) {
        params.push(`level_id=${level.value}`);
    }

    if (theme.value) {
        params.push(`theme_id=${theme.value}`);
    }

    if (params.length) {
        window.location.href = `${CATEGORY_URL}?${params.join('&')}`;
    } else {
        window.location.href = url;
    }
}

const favorite = target => {
    const btn = target.closest('.btn-favorite');
    const postId = btn.getAttribute('post-id-data');

    axios.post(`/post/favorite/${postId}`)
        .then(res => {
            btn.querySelector('.heart-fill').classList.remove('d-none');
            btn.querySelector('.heart').classList.add('d-none');
            btn.classList.add('active');
        })
}

const unfavorite = target => {
    const btn = target.closest('.btn-favorite');
    const postId = btn.getAttribute('post-id-data');

    axios.delete(`/post/favorite/${postId}`)
        .then(res => {
            btn.querySelector('.heart-fill').classList.add('d-none');
            btn.querySelector('.heart').classList.remove('d-none');
            btn.classList.remove('active');
        })
}

const loadPosts = (url, btn) => {

    const postList = document.querySelector('.post-list');

    if (!postList) {
        return;
    }

    axios.get(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
        .then(res => {
            postList.insertAdjacentHTML('beforeend', res.data.html);
            if (res.data.next_page_url) {
                btn.setAttribute('data-url', res.data.next_page_url);
            } else {
                btn.classList.add('d-none');
            }
            lazyLoadInstance.update();
        })
}

const clickEvent = e => {
    const target = e.target;

    if (target.closest('.btn-favorite')) {

        if (target.closest('.btn-favorite').classList.contains('active')) {
            unfavorite(target);
        } else {
            favorite(target);
        }

    }

    if (target.closest('.btn-load-posts')) {
        const btn = target.closest('.btn-load-posts');

        loadPosts(btn.getAttribute('data-url'), btn);
    }
}


const renderBtnFavorite = async () => {

    let ids = [];
    await document.querySelectorAll('.btn-favorite').forEach(btn => {
        ids.push(btn.getAttribute('post-id-data'));
    })

    ids = [...new Set(ids)];

    axios.get(`/post/favorite/check/${ids.join(',')}`)
        .then(res => {
            res.data.forEach(item => {
                document.querySelectorAll(`.btn-favorite[post-id-data="${item.id}"]`).forEach(btn => {
                    if (item.is_favorite) {
                        btn.classList.add('active');
                        btn.querySelector('.heart-fill').classList.remove('d-none');
                    } else {
                        btn.querySelector('.heart').classList.remove('d-none');
                    }
                })
            })
        })
}

renderBtnFavorite();

document.addEventListener('click', clickEvent);

document.addEventListener('change', e => {
    const target = e.target;

    if (target.closest('.filter-select')) {
        filterSelectChange();
    }
});

