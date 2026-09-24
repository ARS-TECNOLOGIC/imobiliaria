{{--
    Editor de corpo do e-mail (sem dependência externa).
    Uso: @include('configuracoes.emails._editor', ['corpo' => $valor])
    Opcional: ['nomeCampo' => 'conteudo'] para field name diferente de "corpo".
--}}
@php
    $nomeCampoEditor = $nomeCampo ?? 'corpo';
    $corpoEditor = old($nomeCampoEditor, $corpo ?? '');
    $formIdAttr = isset($formId) && $formId !== '' ? ' form="'.$formId.'"' : '';
@endphp
<div class="email-editor" data-email-editor>
    <div class="email-editor-toolbar" role="toolbar" aria-label="Formatação">
        <button type="button" class="email-editor-btn" data-cmd="bold" title="Negrito (Ctrl+B)">
            <i class="fa-solid fa-bold"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="italic" title="Itálico (Ctrl+I)">
            <i class="fa-solid fa-italic"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="underline" title="Sublinhado (Ctrl+U)">
            <i class="fa-solid fa-underline"></i>
        </button>
        <span class="email-editor-sep" aria-hidden="true"></span>
        <button type="button" class="email-editor-btn" data-cmd="formatBlock-h2" title="Título">
            <i class="fa-solid fa-heading"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="formatBlock-p" title="Parágrafo">
            <i class="fa-solid fa-paragraph"></i>
        </button>
        <span class="email-editor-sep" aria-hidden="true"></span>
        <button type="button" class="email-editor-btn" data-cmd="insertUnorderedList" title="Lista com marcadores">
            <i class="fa-solid fa-list-ul"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="insertOrderedList" title="Lista numerada">
            <i class="fa-solid fa-list-ol"></i>
        </button>
        <span class="email-editor-sep" aria-hidden="true"></span>
        <button type="button" class="email-editor-btn" data-cmd="createLink" title="Inserir link">
            <i class="fa-solid fa-link"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="unlink" title="Remover link">
            <i class="fa-solid fa-link-slash"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="insertImage" title="Inserir imagem">
            <i class="fa-regular fa-image"></i>
        </button>
        <button type="button" class="email-editor-btn" data-cmd="removeFormat" title="Limpar formatação">
            <i class="fa-solid fa-eraser"></i>
        </button>
        <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none" data-email-editor-file>
    </div>
    <div class="email-editor-imgbar d-none" data-email-editor-imgbar hidden>
        <span class="email-editor-imgbar-label">
            <i class="fa-regular fa-image me-1"></i> Largura
        </span>
        <div class="email-editor-imgbar-presets" role="group" aria-label="Tamanhos predefinidos">
            <button type="button" class="email-editor-imgbar-preset" data-img-width="25">25%</button>
            <button type="button" class="email-editor-imgbar-preset" data-img-width="50">50%</button>
            <button type="button" class="email-editor-imgbar-preset" data-img-width="75">75%</button>
            <button type="button" class="email-editor-imgbar-preset" data-img-width="100">100%</button>
        </div>
        <input
            type="range"
            class="email-editor-imgbar-range"
            min="10"
            max="100"
            step="1"
            value="100"
            data-email-editor-imgrange
            aria-label="Largura da imagem (%)"
        >
        <span class="email-editor-imgbar-value" data-email-editor-imgvalue>100%</span>
        <button type="button" class="email-editor-btn email-editor-imgbar-remove" data-email-editor-imgremove title="Remover imagem">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
    <div
        class="email-editor-content"
        data-email-editor-content
        contenteditable="true"
        role="textbox"
        aria-multiline="true"
        aria-label="Corpo do e-mail"
    >{!! $corpoEditor !!}</div>
    <textarea name="{{ $nomeCampoEditor }}"{!! $formIdAttr !!} class="email-editor-source" data-email-editor-source required aria-hidden="true" tabindex="-1">{{ $corpoEditor }}</textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadImagemUrl = '{{ route('configuracoes.emails.imagens.store') }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function normalizar(html) {
        const vazio = /^(<br\s*\/?>|<p><br\s*\/?><\/p>|<div><br\s*\/?><\/div>|\s)*$/i;
        return vazio.test(html) ? '' : html;
    }

    function sincronizar(editor) {
        const fonte = editor.querySelector('[data-email-editor-source]');
        const area = editor.querySelector('[data-email-editor-content]');
        fonte.value = normalizar(area.innerHTML);
    }

    function focarFim(area) {
        area.focus();
        const sel = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(area);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
    }

    function editorAtivo() {
        const ativo = document.activeElement;
        if (ativo && typeof ativo.closest === 'function') {
            const doFoco = ativo.closest('[data-email-editor]');
            if (doFoco) {
                return doFoco;
            }
        }
        return document.querySelector('[data-email-editor]');
    }

    function inserirTexto(texto) {
        const editor = editorAtivo();
        if (!editor) {
            return;
        }
        const area = editor.querySelector('[data-email-editor-content]');
        const sel = window.getSelection();
        if (!sel.rangeCount || !area.contains(sel.anchorNode)) {
            focarFim(area);
        }
        document.execCommand('insertText', false, texto);
        sincronizar(editor);
    }

    function inserirNoAssunto(nome) {
        const assunto = document.querySelector('[data-campo-assunto]');
        const texto = String.fromCharCode(123, 123) + nome + String.fromCharCode(125, 125);
        if (!assunto) {
            inserirTexto(texto);
            return;
        }
        const inicio = assunto.selectionStart ?? assunto.value.length;
        const fim = assunto.selectionEnd ?? assunto.value.length;
        assunto.value = assunto.value.slice(0, inicio) + texto + assunto.value.slice(fim);
        assunto.focus();
        const pos = inicio + texto.length;
        assunto.setSelectionRange(pos, pos);
    }

    let destinoVariavel = 'corpo';
    const campoAssunto = document.querySelector('[data-campo-assunto]');
    if (campoAssunto) {
        campoAssunto.addEventListener('focus', function () {
            destinoVariavel = 'assunto';
        });
        campoAssunto.addEventListener('click', function () {
            destinoVariavel = 'assunto';
        });
    }

    window.inserirVariavelEmail = function (nome) {
        if (destinoVariavel === 'assunto' && campoAssunto) {
            inserirNoAssunto(nome);
            return;
        }
        inserirTexto(String.fromCharCode(123, 123) + nome + String.fromCharCode(125, 125));
    };

    function estadoAtivo(editor) {
        const area = editor.querySelector('[data-email-editor-content]');
        const map = {
            bold: 'bold',
            italic: 'italic',
            underline: 'underline',
            insertUnorderedList: 'insertUnorderedList',
            insertOrderedList: 'insertOrderedList',
        };

        Object.keys(map).forEach(function (cmd) {
            const btn = editor.querySelector('[data-cmd="' + cmd + '"]');
            if (!btn) {
                return;
            }
            let ativo = false;
            try {
                ativo = document.queryCommandState(map[cmd]);
            } catch (e) {
                ativo = false;
            }
            btn.classList.toggle('is-active', ativo);
        });

        let bloco = '';
        try {
            bloco = (document.queryCommandValue('formatBlock') || '').toLowerCase();
        } catch (e) {
            bloco = '';
        }
        const btnH2 = editor.querySelector('[data-cmd="formatBlock-h2"]');
        const btnP = editor.querySelector('[data-cmd="formatBlock-p"]');
        btnH2?.classList.toggle('is-active', bloco === 'h2');
        btnP?.classList.toggle('is-active', bloco === 'p' || bloco === '' || bloco === 'div');

        if (!area.contains(document.activeElement) && document.activeElement !== area) {
            editor.querySelectorAll('.email-editor-btn.is-active').forEach(function (btn) {
                if (btn.dataset.cmd !== 'insertImage') {
                    btn.classList.remove('is-active');
                }
            });
        }
    }

    function enviarImagem(editor, arquivo) {
        const area = editor.querySelector('[data-email-editor-content]');
        const formData = new FormData();
        formData.append('imagem', arquivo);

        area.setAttribute('contenteditable', 'false');
        editor.classList.add('is-uploading');

        fetch(uploadImagemUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
            .then(function (resultado) {
                if (!resultado.ok || !resultado.data.url) {
                    window.alert(resultado.data.erro || 'Falha ao enviar a imagem.');
                    return;
                }
                area.setAttribute('contenteditable', 'true');
                const sel = window.getSelection();
                if (!sel.rangeCount || !area.contains(sel.anchorNode)) {
                    focarFim(area);
                } else {
                    area.focus();
                }
                document.execCommand('insertHTML', false,
                    '<img src="' + resultado.data.url + '" alt="" style="width:100%;max-width:100%;height:auto;">');
                area.dispatchEvent(new Event('input', { bubbles: true }));
                sincronizar(editor);
                const inserida = area.querySelector('img[src="' + resultado.data.url + '"]');
                if (inserida) {
                    selecionarImagem(editor, inserida);
                }
            })
            .catch(function () {
                window.alert('Erro de rede ao enviar a imagem.');
            })
            .finally(function () {
                area.setAttribute('contenteditable', 'true');
                editor.classList.remove('is-uploading');
            });
    }

    function larguraImagemPercent(img) {
        const explicito = (img.style.width || '').replace(/\s/g, '');
        if (explicito.endsWith('%')) {
            const pct = parseInt(explicito, 10);
            if (!isNaN(pct)) {
                return Math.min(100, Math.max(10, pct));
            }
        }
        if (explicito.endsWith('px')) {
            const px = parseInt(explicito, 10);
            const container = img.closest('[data-email-editor-content]');
            if (!isNaN(px) && container && container.clientWidth) {
                return Math.min(100, Math.max(10, Math.round((px / container.clientWidth) * 100)));
            }
        }
        return 100;
    }

    function aplicarLarguraImagem(img, pct) {
        const valor = Math.min(100, Math.max(10, Math.round(pct)));
        img.style.width = valor + '%';
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
    }

    function limparSelecaoImagem(editor) {
        editor.querySelectorAll('img.is-selected').forEach(function (el) {
            el.classList.remove('is-selected');
        });
        const barra = editor.querySelector('[data-email-editor-imgbar]');
        if (barra) {
            barra.classList.add('d-none');
            barra.hidden = true;
        }
        editor._imgSelecionada = null;
    }

    function selecionarImagem(editor, img) {
        limparSelecaoImagem(editor);
        img.classList.add('is-selected');
        editor._imgSelecionada = img;

        const barra = editor.querySelector('[data-email-editor-imgbar]');
        if (!barra) {
            return;
        }
        const pct = larguraImagemPercent(img);
        const range = barra.querySelector('[data-email-editor-imgrange]');
        const valor = barra.querySelector('[data-email-editor-imgvalue]');
        if (range) {
            range.value = String(pct);
        }
        if (valor) {
            valor.textContent = pct + '%';
        }
        barra.querySelectorAll('[data-img-width]').forEach(function (btn) {
            btn.classList.toggle('is-active', parseInt(btn.dataset.imgWidth, 10) === pct);
        });
        barra.classList.remove('d-none');
        barra.hidden = false;
    }

    function atualizarImagemSelecionada(editor, pct) {
        const img = editor._imgSelecionada;
        if (!img || !img.isConnected) {
            limparSelecaoImagem(editor);
            return;
        }
        aplicarLarguraImagem(img, pct);
        const barra = editor.querySelector('[data-email-editor-imgbar]');
        const range = barra?.querySelector('[data-email-editor-imgrange]');
        const valor = barra?.querySelector('[data-email-editor-imgvalue]');
        const arredondado = Math.min(100, Math.max(10, Math.round(pct)));
        if (range && parseInt(range.value, 10) !== arredondado) {
            range.value = String(arredondado);
        }
        if (valor) {
            valor.textContent = arredondado + '%';
        }
        barra?.querySelectorAll('[data-img-width]').forEach(function (btn) {
            btn.classList.toggle('is-active', parseInt(btn.dataset.imgWidth, 10) === arredondado);
        });
        sincronizar(editor);
        editor.querySelector('[data-email-editor-content]')?.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function imagemAlvoClique(e) {
        const alvo = e.target;
        if (alvo && alvo.nodeType === 1 && alvo.tagName === 'IMG') {
            return alvo;
        }
        return null;
    }

    document.querySelectorAll('[data-email-editor]').forEach(function (editor) {
        if (editor.dataset.editorInit === '1') {
            return;
        }
        editor.dataset.editorInit = '1';

        const area = editor.querySelector('[data-email-editor-content]');
        const fileInput = editor.querySelector('[data-email-editor-file]');
        const barraImg = editor.querySelector('[data-email-editor-imgbar]');
        const rangeImg = barraImg?.querySelector('[data-email-editor-imgrange]');

        if (!area.innerHTML.trim()) {
            area.innerHTML = '<p><br></p>';
        }
        sincronizar(editor);
        estadoAtivo(editor);

        area.addEventListener('input', function () {
            sincronizar(editor);
            estadoAtivo(editor);
        });

        area.addEventListener('keyup', function () {
            estadoAtivo(editor);
        });

        area.addEventListener('mouseup', function () {
            estadoAtivo(editor);
        });

        area.addEventListener('click', function (e) {
            const img = imagemAlvoClique(e);
            if (img && area.contains(img)) {
                e.preventDefault();
                selecionarImagem(editor, img);
                return;
            }
            if (!barraImg?.contains(e.target)) {
                limparSelecaoImagem(editor);
            }
        });

        area.addEventListener('focus', function () {
            destinoVariavel = 'corpo';
            estadoAtivo(editor);
        });

        area.addEventListener('click', function () {
            destinoVariavel = 'corpo';
        });

        document.addEventListener('selectionchange', function () {
            const sel = document.getSelection();
            if (sel && sel.anchorNode && area.contains(sel.anchorNode)) {
                estadoAtivo(editor);
            }
        });

        barraImg?.querySelectorAll('[data-img-width]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                atualizarImagemSelecionada(editor, parseInt(this.dataset.imgWidth, 10));
            });
        });

        rangeImg?.addEventListener('input', function () {
            atualizarImagemSelecionada(editor, parseInt(this.value, 10));
        });

        barraImg?.querySelector('[data-email-editor-imgremove]')?.addEventListener('click', function () {
            const img = editor._imgSelecionada;
            if (img && img.isConnected) {
                img.remove();
                limparSelecaoImagem(editor);
                sincronizar(editor);
                area.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        area.addEventListener('paste', function (e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, texto);
            sincronizar(editor);
        });

        fileInput?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                enviarImagem(editor, this.files[0]);
            }
            this.value = '';
        });

        editor.querySelectorAll('.email-editor-btn').forEach(function (btn) {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
            });
            btn.addEventListener('click', function () {
                const cmd = this.dataset.cmd;
                area.focus();
                if (cmd === 'createLink') {
                    const url = window.prompt('URL do link:', 'https://');
                    if (url) {
                        document.execCommand('createLink', false, url);
                    }
                } else if (cmd === 'insertImage') {
                    fileInput?.click();
                    return;
                } else if (cmd === 'formatBlock-h2') {
                    document.execCommand('formatBlock', false, '<h2>');
                } else if (cmd === 'formatBlock-p') {
                    document.execCommand('formatBlock', false, '<p>');
                } else {
                    document.execCommand(cmd, false, null);
                }
                sincronizar(editor);
                estadoAtivo(editor);
            });
        });

        const fonte = editor.querySelector('[data-email-editor-source]');
        const form = editor.closest('form') || fonte.form;
        if (form) {
            form.addEventListener('submit', function () {
                sincronizar(editor);
            });
            form.querySelectorAll('[type="submit"]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    sincronizar(editor);
                });
            });
            if (form.id) {
                document.querySelectorAll('button[form="' + form.id + '"][type="submit"]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        sincronizar(editor);
                    });
                });
            }
            fonte.addEventListener('invalid', function () {
                sincronizar(editor);
            });
        }
    });
});
</script>
