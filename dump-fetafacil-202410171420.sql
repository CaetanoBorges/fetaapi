PGDMP  ;                	    |         	   fetafacil    17rc1    17rc1 V    f           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            g           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            h           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            i           1262    32768 	   fetafacil    DATABASE     �   CREATE DATABASE fetafacil WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Portuguese_Luxembourg.1252';
    DROP DATABASE fetafacil;
                     postgres    false                        2615    57345    public    SCHEMA        CREATE SCHEMA public;
    DROP SCHEMA public;
                     pg_database_owner    false            j           0    0    SCHEMA public    COMMENT     6   COMMENT ON SCHEMA public IS 'standard public schema';
                        pg_database_owner    false    5            k           0    0    SCHEMA public    ACL     +   REVOKE USAGE ON SCHEMA public FROM PUBLIC;
                        pg_database_owner    false    5            �            1259    57346    cliente    TABLE     q   CREATE TABLE public.cliente (
    identificador character varying(500) NOT NULL,
    empresa boolean NOT NULL
);
    DROP TABLE public.cliente;
       public         heap r       postgres    false    5            �            1259    57349    configuracao    TABLE     �   CREATE TABLE public.configuracao (
    cliente_identificador character varying(500) NOT NULL,
    tempo_bloqueio integer NOT NULL,
    auto_pagamento_recebimento boolean NOT NULL,
    pin text NOT NULL,
    identificador bigint NOT NULL
);
     DROP TABLE public.configuracao;
       public         heap r       postgres    false    5            �            1259    57354    configuracao_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.configuracao_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.configuracao_identificador_seq;
       public               postgres    false    5    218            l           0    0    configuracao_identificador_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.configuracao_identificador_seq OWNED BY public.configuracao.identificador;
          public               postgres    false    219            �            1259    57355 	   confirmar    TABLE       CREATE TABLE public.confirmar (
    cliente_identificador character varying,
    acao character varying NOT NULL,
    codigo_enviado character varying,
    quando character varying NOT NULL,
    confirmou boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.confirmar;
       public         heap r       postgres    false    5            �            1259    57360    confirmar_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.confirmar_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.confirmar_identificador_seq;
       public               postgres    false    220    5            m           0    0    confirmar_identificador_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.confirmar_identificador_seq OWNED BY public.confirmar.identificador;
          public               postgres    false    221            �            1259    57361    contacto    TABLE     �   CREATE TABLE public.contacto (
    cliente_identificador character varying(500) NOT NULL,
    telefone character varying(500) NOT NULL,
    email character varying(500),
    atual boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.contacto;
       public         heap r       postgres    false    5            �            1259    57366    contacto_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.contacto_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 1   DROP SEQUENCE public.contacto_identificador_seq;
       public               postgres    false    222    5            n           0    0    contacto_identificador_seq    SEQUENCE OWNED BY     Y   ALTER SEQUENCE public.contacto_identificador_seq OWNED BY public.contacto.identificador;
          public               postgres    false    223            �            1259    57367    deposito    TABLE     b  CREATE TABLE public.deposito (
    transacao_pid character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL
);
    DROP TABLE public.deposito;
       public         heap r       postgres    false    5            �            1259    57372    empresa    TABLE     F  CREATE TABLE public.empresa (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    nif character varying(500) NOT NULL,
    nome character varying(500) NOT NULL,
    area_atuacao character varying(500) NOT NULL,
    balanco numeric(16,2),
    foto text NOT NULL
);
    DROP TABLE public.empresa;
       public         heap r       postgres    false    5            �            1259    57377    endereco    TABLE       CREATE TABLE public.endereco (
    provincia character varying(500),
    cidade character varying(500),
    bairro character varying(500),
    cliente_identificador character varying NOT NULL,
    atual boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.endereco;
       public         heap r       postgres    false    5            �            1259    57382    endereco_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.endereco_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 1   DROP SEQUENCE public.endereco_identificador_seq;
       public               postgres    false    226    5            o           0    0    endereco_identificador_seq    SEQUENCE OWNED BY     Y   ALTER SEQUENCE public.endereco_identificador_seq OWNED BY public.endereco.identificador;
          public               postgres    false    227            �            1259    57383    extrato    TABLE     �  CREATE TABLE public.extrato (
    identificador_conta character varying(500) NOT NULL,
    transacao_pid character varying(500) NOT NULL,
    entrada boolean NOT NULL,
    movimento numeric(16,2) NOT NULL,
    balanco numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.extrato;
       public         heap r       postgres    false    5            �            1259    57388    extrato_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.extrato_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 0   DROP SEQUENCE public.extrato_identificador_seq;
       public               postgres    false    5    228            p           0    0    extrato_identificador_seq    SEQUENCE OWNED BY     W   ALTER SEQUENCE public.extrato_identificador_seq OWNED BY public.extrato.identificador;
          public               postgres    false    229            �            1259    57389    levantamento    TABLE     �  CREATE TABLE public.levantamento (
    transacao_pid character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    identificador bigint NOT NULL
);
     DROP TABLE public.levantamento;
       public         heap r       postgres    false    5            �            1259    57394    levantamento_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.levantamento_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.levantamento_identificador_seq;
       public               postgres    false    5    230            q           0    0    levantamento_identificador_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.levantamento_identificador_seq OWNED BY public.levantamento.identificador;
          public               postgres    false    231            �            1259    57395 	   parcelado    TABLE     �  CREATE TABLE public.parcelado (
    transacao_pid json NOT NULL,
    parcelas character varying(500) NOT NULL,
    valor_parcela numeric(16,2) NOT NULL,
    valor_total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano bit varying(500) NOT NULL,
    estado boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.parcelado;
       public         heap r       postgres    false    5            �            1259    57400    parcelado_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.parcelado_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.parcelado_identificador_seq;
       public               postgres    false    5    232            r           0    0    parcelado_identificador_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.parcelado_identificador_seq OWNED BY public.parcelado.identificador;
          public               postgres    false    233            �            1259    57401 
   particular    TABLE     [  CREATE TABLE public.particular (
    cliente_identificador character varying(500) NOT NULL,
    bi character varying(500),
    nome character varying(500) NOT NULL,
    genero character varying(500),
    nascimento character varying(500),
    balanco numeric(16,2) NOT NULL,
    identificador character varying NOT NULL,
    foto text NOT NULL
);
    DROP TABLE public.particular;
       public         heap r       postgres    false    5            �            1259    57406 
   recorrente    TABLE     �  CREATE TABLE public.recorrente (
    transacao_pid json NOT NULL,
    periodicidade character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    valor numeric(16,2) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    estado boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.recorrente;
       public         heap r       postgres    false    5            �            1259    57487    recorrente_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.recorrente_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 3   DROP SEQUENCE public.recorrente_identificador_seq;
       public               postgres    false    5    235            s           0    0    recorrente_identificador_seq    SEQUENCE OWNED BY     ]   ALTER SEQUENCE public.recorrente_identificador_seq OWNED BY public.recorrente.identificador;
          public               postgres    false    237            �            1259    57411 	   transacao    TABLE     �  CREATE TABLE public.transacao (
    pid character varying(500) NOT NULL,
    tipo character varying(500),
    de character varying(500),
    para character varying(500),
    onde character varying(500) NOT NULL,
    quando character varying(500),
    descricao character varying NOT NULL,
    valor numeric(16,2) NOT NULL,
    dia character varying NOT NULL,
    mes character varying NOT NULL,
    ano character varying NOT NULL,
    executado boolean NOT NULL,
    identificador_conta character varying
);
    DROP TABLE public.transacao;
       public         heap r       postgres    false    5            �           2604    57416    configuracao identificador    DEFAULT     �   ALTER TABLE ONLY public.configuracao ALTER COLUMN identificador SET DEFAULT nextval('public.configuracao_identificador_seq'::regclass);
 I   ALTER TABLE public.configuracao ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    219    218            �           2604    57417    confirmar identificador    DEFAULT     �   ALTER TABLE ONLY public.confirmar ALTER COLUMN identificador SET DEFAULT nextval('public.confirmar_identificador_seq'::regclass);
 F   ALTER TABLE public.confirmar ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    221    220            �           2604    57418    contacto identificador    DEFAULT     �   ALTER TABLE ONLY public.contacto ALTER COLUMN identificador SET DEFAULT nextval('public.contacto_identificador_seq'::regclass);
 E   ALTER TABLE public.contacto ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    223    222            �           2604    57419    endereco identificador    DEFAULT     �   ALTER TABLE ONLY public.endereco ALTER COLUMN identificador SET DEFAULT nextval('public.endereco_identificador_seq'::regclass);
 E   ALTER TABLE public.endereco ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    227    226            �           2604    57420    extrato identificador    DEFAULT     ~   ALTER TABLE ONLY public.extrato ALTER COLUMN identificador SET DEFAULT nextval('public.extrato_identificador_seq'::regclass);
 D   ALTER TABLE public.extrato ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    229    228            �           2604    57421    levantamento identificador    DEFAULT     �   ALTER TABLE ONLY public.levantamento ALTER COLUMN identificador SET DEFAULT nextval('public.levantamento_identificador_seq'::regclass);
 I   ALTER TABLE public.levantamento ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    231    230            �           2604    57422    parcelado identificador    DEFAULT     �   ALTER TABLE ONLY public.parcelado ALTER COLUMN identificador SET DEFAULT nextval('public.parcelado_identificador_seq'::regclass);
 F   ALTER TABLE public.parcelado ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    233    232            �           2604    57488    recorrente identificador    DEFAULT     �   ALTER TABLE ONLY public.recorrente ALTER COLUMN identificador SET DEFAULT nextval('public.recorrente_identificador_seq'::regclass);
 G   ALTER TABLE public.recorrente ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    237    235            O          0    57346    cliente 
   TABLE DATA           9   COPY public.cliente (identificador, empresa) FROM stdin;
    public               postgres    false    217   n       P          0    57349    configuracao 
   TABLE DATA           }   COPY public.configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin, identificador) FROM stdin;
    public               postgres    false    218   ?n       R          0    57355 	   confirmar 
   TABLE DATA           r   COPY public.confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou, identificador) FROM stdin;
    public               postgres    false    220   �n       T          0    57361    contacto 
   TABLE DATA           `   COPY public.contacto (cliente_identificador, telefone, email, atual, identificador) FROM stdin;
    public               postgres    false    222   3o       V          0    57367    deposito 
   TABLE DATA           ^   COPY public.deposito (transacao_pid, agente, notas, total, quando, dia, mes, ano) FROM stdin;
    public               postgres    false    224   �o       W          0    57372    empresa 
   TABLE DATA           o   COPY public.empresa (identificador, cliente_identificador, nif, nome, area_atuacao, balanco, foto) FROM stdin;
    public               postgres    false    225   �o       X          0    57377    endereco 
   TABLE DATA           j   COPY public.endereco (provincia, cidade, bairro, cliente_identificador, atual, identificador) FROM stdin;
    public               postgres    false    226   p       Z          0    57383    extrato 
   TABLE DATA           �   COPY public.extrato (identificador_conta, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano, identificador) FROM stdin;
    public               postgres    false    228   Kp       \          0    57389    levantamento 
   TABLE DATA           q   COPY public.levantamento (transacao_pid, agente, notas, total, quando, dia, mes, ano, identificador) FROM stdin;
    public               postgres    false    230   aq       ^          0    57395 	   parcelado 
   TABLE DATA           �   COPY public.parcelado (transacao_pid, parcelas, valor_parcela, valor_total, quando, dia, mes, ano, estado, identificador) FROM stdin;
    public               postgres    false    232   ~q       `          0    57401 
   particular 
   TABLE DATA           w   COPY public.particular (cliente_identificador, bi, nome, genero, nascimento, balanco, identificador, foto) FROM stdin;
    public               postgres    false    234   �q       a          0    57406 
   recorrente 
   TABLE DATA           w   COPY public.recorrente (transacao_pid, periodicidade, quando, valor, dia, mes, ano, estado, identificador) FROM stdin;
    public               postgres    false    235   	r       b          0    57411 	   transacao 
   TABLE DATA           �   COPY public.transacao (pid, tipo, de, para, onde, quando, descricao, valor, dia, mes, ano, executado, identificador_conta) FROM stdin;
    public               postgres    false    236   &r       t           0    0    configuracao_identificador_seq    SEQUENCE SET     L   SELECT pg_catalog.setval('public.configuracao_identificador_seq', 9, true);
          public               postgres    false    219            u           0    0    confirmar_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.confirmar_identificador_seq', 3, true);
          public               postgres    false    221            v           0    0    contacto_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.contacto_identificador_seq', 24, true);
          public               postgres    false    223            w           0    0    endereco_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.endereco_identificador_seq', 24, true);
          public               postgres    false    227            x           0    0    extrato_identificador_seq    SEQUENCE SET     H   SELECT pg_catalog.setval('public.extrato_identificador_seq', 16, true);
          public               postgres    false    229            y           0    0    levantamento_identificador_seq    SEQUENCE SET     M   SELECT pg_catalog.setval('public.levantamento_identificador_seq', 1, false);
          public               postgres    false    231            z           0    0    parcelado_identificador_seq    SEQUENCE SET     J   SELECT pg_catalog.setval('public.parcelado_identificador_seq', 1, false);
          public               postgres    false    233            {           0    0    recorrente_identificador_seq    SEQUENCE SET     K   SELECT pg_catalog.setval('public.recorrente_identificador_seq', 1, false);
          public               postgres    false    237            �           2606    57424    cliente cliente_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.cliente DROP CONSTRAINT cliente_pkey;
       public                 postgres    false    217            �           2606    57426 3   configuracao configuracao_cliente_identificador_key 
   CONSTRAINT        ALTER TABLE ONLY public.configuracao
    ADD CONSTRAINT configuracao_cliente_identificador_key UNIQUE (cliente_identificador);
 ]   ALTER TABLE ONLY public.configuracao DROP CONSTRAINT configuracao_cliente_identificador_key;
       public                 postgres    false    218            �           2606    57428    configuracao configuracao_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.configuracao
    ADD CONSTRAINT configuracao_pk PRIMARY KEY (identificador);
 F   ALTER TABLE ONLY public.configuracao DROP CONSTRAINT configuracao_pk;
       public                 postgres    false    218            �           2606    57430    confirmar confirmar_pk 
   CONSTRAINT     _   ALTER TABLE ONLY public.confirmar
    ADD CONSTRAINT confirmar_pk PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.confirmar DROP CONSTRAINT confirmar_pk;
       public                 postgres    false    220            �           2606    57432    contacto contacto_email_key 
   CONSTRAINT     W   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_email_key UNIQUE (email);
 E   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_email_key;
       public                 postgres    false    222            �           2606    57434    contacto contacto_pk 
   CONSTRAINT     ]   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_pk PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_pk;
       public                 postgres    false    222            �           2606    57436    contacto contacto_telefone_key 
   CONSTRAINT     ]   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_telefone_key UNIQUE (telefone);
 H   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_telefone_key;
       public                 postgres    false    222            �           2606    57438    empresa empresa_nif_key 
   CONSTRAINT     Q   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_nif_key UNIQUE (nif);
 A   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_nif_key;
       public                 postgres    false    225            �           2606    57440    empresa empresa_nif_key1 
   CONSTRAINT     R   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_nif_key1 UNIQUE (nif);
 B   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_nif_key1;
       public                 postgres    false    225            �           2606    57442    empresa empresa_pkey 
   CONSTRAINT     t   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (identificador, cliente_identificador);
 >   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_pkey;
       public                 postgres    false    225    225            �           2606    57444    endereco endereco_pk 
   CONSTRAINT     ]   ALTER TABLE ONLY public.endereco
    ADD CONSTRAINT endereco_pk PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.endereco DROP CONSTRAINT endereco_pk;
       public                 postgres    false    226            �           2606    57446    extrato extrato_pk 
   CONSTRAINT     [   ALTER TABLE ONLY public.extrato
    ADD CONSTRAINT extrato_pk PRIMARY KEY (identificador);
 <   ALTER TABLE ONLY public.extrato DROP CONSTRAINT extrato_pk;
       public                 postgres    false    228            �           2606    57448    levantamento levantamento_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.levantamento
    ADD CONSTRAINT levantamento_pk PRIMARY KEY (identificador);
 F   ALTER TABLE ONLY public.levantamento DROP CONSTRAINT levantamento_pk;
       public                 postgres    false    230            �           2606    57450    parcelado parcelado_pk 
   CONSTRAINT     _   ALTER TABLE ONLY public.parcelado
    ADD CONSTRAINT parcelado_pk PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.parcelado DROP CONSTRAINT parcelado_pk;
       public                 postgres    false    232            �           2606    57452    particular particular_bi_key 
   CONSTRAINT     U   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_bi_key UNIQUE (bi);
 F   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_bi_key;
       public                 postgres    false    234            �           2606    57454 /   particular particular_cliente_identificador_key 
   CONSTRAINT     {   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_cliente_identificador_key UNIQUE (cliente_identificador);
 Y   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_cliente_identificador_key;
       public                 postgres    false    234            �           2606    57456    particular particular_pk 
   CONSTRAINT     a   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_pk PRIMARY KEY (identificador);
 B   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_pk;
       public                 postgres    false    234            �           2606    57495    recorrente recorrente_pk 
   CONSTRAINT     a   ALTER TABLE ONLY public.recorrente
    ADD CONSTRAINT recorrente_pk PRIMARY KEY (identificador);
 B   ALTER TABLE ONLY public.recorrente DROP CONSTRAINT recorrente_pk;
       public                 postgres    false    235            �           2606    57458    transacao unique_transacao 
   CONSTRAINT     T   ALTER TABLE ONLY public.transacao
    ADD CONSTRAINT unique_transacao UNIQUE (pid);
 D   ALTER TABLE ONLY public.transacao DROP CONSTRAINT unique_transacao;
       public                 postgres    false    236            �           2606    57459    empresa fk_empresa    FK CONSTRAINT     �   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT fk_empresa FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 <   ALTER TABLE ONLY public.empresa DROP CONSTRAINT fk_empresa;
       public               postgres    false    225    217    4759            �           2606    57464    particular fk_particular    FK CONSTRAINT     �   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT fk_particular FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 B   ALTER TABLE ONLY public.particular DROP CONSTRAINT fk_particular;
       public               postgres    false    4759    234    217            O   )   x�3374063N5NI4H�L�2�-L�R�$g	W� ���      P      x��λ!���AHz@/N���v݅��&�X4\�6��h���T�V	�9S�*疁����J1�x�I��̮���
G��;h]-�qY�8<�d�U�2�IT 5m�����S�����:���?hkP�      R   U   x�m��	�0�s2E�4�iJ��� ^��U��x��9�z5m}���N�yE&�()"A�JjE�2�A`�IYL��rK��A�׉���      T   D   x�%ɻ� ��&�'/�	h�`���\s�["W�aC*�E�?�ũ����z�jl	 ���~����      V      x������ � �      W   S   x�E�1�  ��}/ B�q!P�� Q����'GA(��R��	�x�$�g�WSS��6n}2��Tp��DKU���ӎ��j��Q      X   4   x����#3sCc3�T�D�D�NCs�9KS�T 	�32����� �U�      Z     x�}�Kn�0Eѱ�K�����du:+���PJN��@GO�VC��/��$+G�.ZIw�� l�d�I�}iǭ��� `/nl�;�x�`%�\GJ�.zN��&ܛ�`��d�.�8�$�!Q;G5Q	J�)u�:u�hM��U��t��%�Z�|^�t���M}ңQ�/~>#���:�.�z�rDE���!Y��EՇ\TEL���Y���e�D5��Ϸ����#lTjv%Y:wGY�1�,��0�75��1�2.��c߶����      \      x������ � �      ^      x������ � �      `   ^   x�E̻�  �:L�r�@0�����h#�����7��L8X(�
È�9����`?�5E��e�BJ-�-�0�`�B��e(��=��W7{����/      a      x������ � �      b   �   x����n1г�/�;��K/+��B)B���-t�Q�gK/���ߧ����(y�UUh:�m`I�y����LY%��r"v�9�H�5��u�ca��5 #|9�=�ާ5|g]P�ߨvQ�P�Y妚��r�_�*Vpf97��n�R�l��Ʈ��ү�M׻5�z���Z���j��+v}�Y֑u��MxC_���     